<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.03.2025
 * Time: 12:45
 */

namespace IdImage\Command;


use IdImage\Abstracts\CommandAbstract;

class CreationCommand extends CommandAbstract
{
    protected ?string $operation = 'creation';

    public function run()
    {
        $this->cli->title('Creating products');

        # $limit = $this->idImage->limitCreation();
        $action = 'mgr/actions/product/creation';
        $response = $this->idImage->runProcessor($action, [
            'steps' => true,
        ]);

        if ($response->isError()) {
            $this->cli->info('Error: '.$response->getMessage());

            return false;
        }
        $data = $response->getObject();

        $total = $data['total'];
        $iterations = $data['iterations'];

        $this->cli->info('Total products: '.$total);

        // Создать пошаговый процесс по согласно лимиту $limit
        $created = 0;
        $updated = 0;
        $created_thumbnail = 0;
        $task_upload = 0;
        if ($iterations) {
            foreach ($iterations as $i => $ids) {
                $this->idImage->modx->error->reset();

                $time = microtime(true);


                // Создать товары
                $response = $this->idImage->runProcessor($action, [
                    'ids' => $ids,
                ]);

                if ($response->isError()) {
                    echo $response->getMessage();
                    continue;
                }

                $data = $response->getObject();

                $stat = $data['stat'];
                if (!empty($stat['created'])) {
                    $created += $stat['created'];
                }
                if (!empty($stat['updated'])) {
                    $updated += $stat['updated'];
                }
                if (!empty($stat['created_thumbnail'])) {
                    $created_thumbnail += $stat['created_thumbnail'];
                }
                if (!empty($stat['task_upload'])) {
                    $task_upload += $stat['task_upload'];
                }

                $time = round(microtime(true) - $time, 2).' s';
                $this->cli->info('[iteration:'.$i.'][time:'.$time.'] products: '.$data['total']);
            }
        }
        $this->cli->info('Created: '.$created);
        $this->cli->info('Updated: '.$updated);
        $this->cli->info('Created thumbnail: '.$created_thumbnail);
        $this->cli->info('Task upload: '.$task_upload);
        $this->cli->info('Completed');

        return true;
    }
}
