<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */

class idImageOperation
{
    private idImage $idImage;

    public function __construct(idImage $idImage)
    {
        $this->idImage = $idImage;
    }

    public function statusPoll(idImageClose $Close, array $item, $save = true)
    {
        $status = $item['status'] ?? idImageClose::STATUS_ERROR;
        $version = $item['version'] ?? 0;
        $total_close = $item['total_close'] ?? 0;
        $min_scope = $item['min_scope'] ?? 0;
        $closes = $item['closes'] ?? [];

        $Close->set('status', $status);
        $Close->set('version', $version);
        $Close->set('total_close', $total_close);
        $Close->set('min_scope', $min_scope);
        $Close->set('closes', $closes);

        if (!$save) {
            return true;
        }

        return $Close->save();
    }

    public function upload(idImageClose $Close, $check = false)
    {
        $id = $Close->get('pid');
        if ($id <= 0) {
            throw new Exception('Invalid pid, can not upload');
        }

        // Проверяем наличие файла на диске
        $imagePath = MODX_BASE_PATH.$Close->get('picture');
        if (!file_exists($imagePath)) {
            $Close->set('status', idImageClose::STATUS_ERROR);

            return false;
        }

        if ($check) {
            if ($Close->change($imagePath)) {
                return true;
            }
        }

        try {
            // Готовим изображение
            $this->idImage->makeThumbnail($imagePath, function ($path) use ($id, $Close) {
                // Отправляем изображения в сервис
                $response = $this->idImage->client()->upload($id, $path)->send();
                // Записываем ответ

                $status = $response->isOk() ? idImageClose::STATUS_BUILD : idImageClose::STATUS_ERROR;

                $Close->set('status', $status);
                $Close->set('status_code', $response->getStatus());

                // Ставим метку о доставке
                $Close->set('received', $response->isOk());

                // Пишем дату доставки
                $Close->set('received_at', time());
            });
        } catch (Exception $e) {
            $Close->set('status', idImageClose::STATUS_ERROR);
        }


        return $Close->save();
    }

    public function create(int $id, string $imagePath)
    {
        if (!file_exists($imagePath)) {
            return false;
        }

        /* @var idImageClose $Close */
        if (!$Close = $this->idImage->modx->getObject('idImageClose', ['pid' => $id])) {
            $Close = $this->idImage->modx->newObject('idImageClose');
        }

        $Close->set('hash', $Close->createHash($imagePath));
        $Close->set('pid', $id);
        $Close->set('picture', str_ireplace(MODX_BASE_PATH, '', $imagePath));
        $Close->set('status', idImageClose::STATUS_PROCESS);

        return $Close->save();
    }
}
