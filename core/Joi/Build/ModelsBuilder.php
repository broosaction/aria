<?php


namespace Core\Joi\Build;

use Core\Drivers\ORM\Observer\ModelObserver;
use Core\Joi\Start;
use Core\Joi\System\Utils;
use Doctrine\Inflector\CachedWordInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\Rules;
use Doctrine\Inflector\RulesetInflector;
use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Core\Drivers\ORM\Database\Model;

class ModelsBuilder extends Builder
{

    private Start $server;


    public function build()
    {

        $tableInfos = [];

        foreach ($this->getTableNames() as $tableName) {

            $tableInfos[] = [
                'name' => $tableName,
                'class' => $this->classify($tableName, true)
            ];
        }

        foreach ($tableInfos as $tableInfo) {
            $namespace = new PhpNamespace('App\\Models');
            // $namespace->addUse('');
            $class = $namespace->addClass($tableInfo['class']);

            $class->addExtend(Model::class);
            $class->addProperty("_tableName", $tableInfo['name'])->setStatic(true)
                ->isProtected();
            $primary_key = '';
            $primary_key_auto = false;
            $has_created_at = false;
            $has_updated_at = false;
            $has_deleted_at = false;
            foreach ($this->getTableColumns($tableInfo['name']) as $column) {
                $props = $class->addProperty($column['Field']);

                if ($column['Default'] !== null) {
                    if (Utils::dbTypeToLocal($column['Type']) === 'int') {

                        $props->setValue((int)$column['Default']);

                    } else {
                        $props->setValue($column['Default']);
                    }

                    if ($column['Field'] === 'created_at') {
                        $has_created_at = true;
                    }
                    if ($column['Field'] === 'updated_at') {
                        $has_updated_at = true;
                    }
                    if ($column['Field'] === 'deleted_at') {
                        $has_deleted_at = true;
                    }

                }

                if ($column['Key'] === 'PRI') {
                    $primary_key = $column['Field'];
                    if ($column['Extra'] === 'auto_increment') {
                        $primary_key_auto = true;
                    }
                }

                $props->isPublic();
            }

            if($has_created_at === false){
                $this->addColumn('created_at', $tableInfo['name']);
                $class->addProperty('created_at')->isPublic();
            }

            if($has_updated_at === false){
                $this->addColumn('updated_at', $tableInfo['name']);
                $class->addProperty('updated_at')->isPublic();
            }

            if($has_deleted_at === false){
                $this->addColumn('deleted_at', $tableInfo['name']);
                 $class->addProperty('deleted_at')->isPublic();
            }


            $class->addMethod('getTable')->addBody('return "' . $tableInfo['name'] . '";')->isProtected();

            if ($primary_key !== '') {
                $class->addMethod('getID')->addBody('return "' . $primary_key . '";')->isProtected();
            }

            if ($primary_key_auto === false) {

                $class->addMethod('autoIncrementId')->addBody('return ' . $primary_key_auto . ';')->isProtected();
            }

            $configdata = "<?php \n" . $namespace . "\n ";

            FileSystem::write($this->server->server_home . '/app/Models/' . $tableInfo['class'] . '.php', $configdata);


            $observerNS = new PhpNamespace('App\\Observers');
            $oCls = $observerNS->addClass($tableInfo['class'] . 'Observer')->addComment('Auto generated on ' . date('d/m/Y m:s'));
            $oCls->addImplement(ModelObserver::class);

            $oCls->addMethod('create')->addParameter('' . $tableInfo['name']);
            $oCls->addMethod('update')->addParameter('' . $tableInfo['name']);
            $oCls->addMethod('delete')->addParameter('' . $tableInfo['name']);
            $oCls->addMethod('restore')->addParameter('' . $tableInfo['name']);
            $oCls->addMethod('forceDelete')->addParameter('' . $tableInfo['name']);

            $obData = "<?php \n" . $observerNS . "\n ";

            FileSystem::write($this->server->server_home . '/app/Observers/' . $tableInfo['class'] . 'Observer.php', $obData);

        }

        $this->output->writeln('Done! Your models are in the Models/ directory. Move them wherever you want!');


    }

    public function getTableNames()
    {
        $db = $this->server->getDatabase()->getDatabaseEngine();
        $tablesRaw = $db->fetchAll('SHOW TABLES');

        $tableNames = [];
        foreach ($tablesRaw as $table) {
            $tableNames[] = $table[0];
        }

        return $tableNames;
    }

    public function getTableColumns($tableName)
    {
        return $this->server->getDatabase()->getDatabaseEngine()->fetchAll('DESCRIBE ' . $tableName);
    }


    protected function classify($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace('-', '', ucwords($string, '-'));
        $str = str_replace('_', '', ucwords($str, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }


    /**
     * @param Start $server
     */
    public function setServer(Start $server): ModelsBuilder
    {
        $this->server = $server;

        return $this;
    }

    private function addColumn($name, $table){
        return $this->server->getDatabase()->getDatabaseEngine()->query('ALTER TABLE '.$table.' ADD COLUMN '.$name.' VARCHAR(100) NULL ' );
    }

}