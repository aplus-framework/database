Database
========

.. image:: image.png
   :alt: Aplus Framework Database Library

Aplus Framework Database Library.

- `Installation`_
- `Basic Usage`_
- `Data Manipulation Language - DML`_
- `Data Definition Language - DDL`_

Installation
------------

The installation of this library can be done with Composer:

.. code-block::

    composer require aplus/database

Basic Usage
-----------

Connection
^^^^^^^^^^

.. code-block:: php

    use Framework\Database\Database;

    $database = new Database($username, $password, $schema, $host, $port, $logger);

.. code-block:: php

    use Framework\Database\Database;

    $database = new Database($config);

.. code-block:: php

    $default = [
        'host' => 'localhost',
        'port' => 3306,
        'username' => null,
        'password' => null,
        'schema' => null,
        'socket' => null,
        'engine' => 'InnoDB',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'timezone' => '+00:00',
        'ssl' => [
            'enabled' => false,
            'verify' => true,
            'key' => null,
            'cert' => null,
            'ca' => null,
            'capath' => null,
            'cipher' => null,
        ],
        'failover' => [],
        'options' => [
            MYSQLI_OPT_CONNECT_TIMEOUT => 10,
            MYSQLI_OPT_INT_AND_FLOAT_NATIVE => true,
            MYSQLI_OPT_LOCAL_INFILE => 1,
        ],
        'report' => MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX,
    ];

Executing Queries
^^^^^^^^^^^^^^^^^

query

.. code-block:: php

    $result = $database->query('SELECT * FROM Users WHERE id = 1'); // Result

.. code-block:: php

    $id = $database->quote($_GET['user_id']);
    $result = $database->query('SELECT * FROM Users WHERE id = ' . $id); // Result

exec

.. code-block:: php

    $affectedRows = $database->exec('INSERT INTO Users SET name = "John Doe"'); // int

.. code-block:: php

    $name = $database->quote($_POST['name']);
    $affectedRows = $database->exec('INSERT INTO Users SET name = ' $name); // int

Prepared Statement
^^^^^^^^^^^^^^^^^^

.. code-block:: php

    $preparedStatement = $database->prepare('SELECT * FROM Users WHERE id = ?'); // PreparedStatement

.. code-block:: php

    $result = $database->prepare('SELECT * FROM Users WHERE id = ?')->query(5); // Result

.. code-block:: php

    $idGreaterThan = 3;
    $nameLike = 'John %';
    $result = $database->prepare('SELECT * FROM Users WHERE id > ? AND name LIKE ?')
                       ->query($idGreaterThan, $nameLike); // Result

.. code-block:: php

    $affectedRows = $database->prepare('INSERT INTO Users SET name = ?')
                             ->exec($_POST['name']); // int

Result
^^^^^^

.. code-block:: php

    $result = $database->query('SELECT * FROM Users'); // Result
    $first = $result->fetch(); // object or null
    $others = $result->fetchAll(); // array of objects or empty array
    $userOnRow10 = $result->fetchRow(10); // object or null

Data Manipulation Language - DML
--------------------------------

SELECT
^^^^^^

.. code-block:: php

    $result = $database->select()
                       ->from('Users')
                       ->where('id', '<', 5)
                       ->run(); // Result

    // HTML table rows with users data
    while($user = $result->fetch()) {
        echo '<tr>';
        echo '<td>' . $user->id . '</td>';
        echo '<td>' . htmlentities($user->name) . '</td>';
        echo '</tr>';
    }

.. code-block:: php

    $sql = $database->select()
                    ->from('Users')
                    ->where('id', '<', $_GET['user_id'])
                    ->sql(); // string

.. code-block:: sql

    SELECT
     *
     FROM `Users`
     WHERE `id` < '5;drop table Users;'

INSERT
^^^^^^

.. code-block:: php

    $affectedRows = $database->insert()
                             ->into('Users')
                             ->columns('name', 'email')
                             ->values([
                                 ['John', 'foo@baz.com'],
                                 ['Mary', 'bar@baz.com'],
                             ])->run();

.. code-block:: sql

    INSERT
     INTO `Users`
     (`name`, `email`)
     VALUES ('John', 'foo@baz.com'),
     ('Mary', 'bar@baz.com')

UPDATE
^^^^^^

.. code-block:: php

    $database->update()
             ->table('Users')
             ->set(['name' => 'Johnny']);
             ->whereEqual('id', 1)
             ->run();

.. code-block:: sql

    UPDATE
     `Users`
     SET `name` = 'Johnny'
     WHERE `id` = 1

DELETE
^^^^^^

.. code-block:: php

    $database->delete()
             ->from('Users');
             ->whereEqual('id', 88)
             ->run();

.. code-block:: sql

    DELETE
     FROM `Users`
     WHERE `id` = 88

REPLACE
^^^^^^^

.. code-block:: php

    $database->replace()
             ->into('Users')
             ->columns('id', 'name', 'email')
             ->values(1, 'John Doe', 'johndoe@ecorp.tld')
             ->run();

.. code-block:: sql

    REPLACE
     INTO `Users`
     (`id`, `name`, `email`)
     VALUES (1, 'John Doe', 'johndoe@ecorp.tld')

WITH
^^^^

LOAD DATA
^^^^^^^^^

.. code-block:: php

    use Framework\Database\Manipulation\LoadData;

    $database->loadData()
             ->infile('/home/developer/users.csv')
             ->options(LoadData::OPT_LOCAL)
             ->intoTable('Users')
             ->charset('utf8')
             ->columnsTerminatedBy(',')
             ->run();

.. code-block:: sql

    LOAD DATA
    LOCAL
     INFILE '/home/developer/users.csv'
     INTO TABLE `Users`
     CHARACTER SET utf8
     COLUMNS
      TERMINATED BY ','

Data Definition Language - DDL
------------------------------

CREATE SCHEMA
^^^^^^^^^^^^^

.. code-block:: php

    $database->createSchema('app')->run();

.. code-block:: sql

    CREATE SCHEMA `app`

ALTER SCHEMA
^^^^^^^^^^^^

.. code-block:: php

    $database->alterSchema('app')->charset('utf8')->run();

.. code-block:: sql

    ALTER SCHEMA `app`
     CHARACTER SET = 'utf8'

DROP SCHEMA
^^^^^^^^^^^

.. code-block:: php

    $database->dropSchema('app')->run();

.. code-block:: sql

    DROP SCHEMA `app`

CREATE TABLE
^^^^^^^^^^^^

.. code-block:: php

    use Framework\Database\Definition\Table\TableDefinition;

    $database->createTable('Users')
             ->definition(function (TableDefinition $def) {
                $def->column('id')->int(11)->primaryKey();
                $def->column('email')->varchar(255);
                $def->column('name')->varchar(32)->null();
                $def->column('type')
                    ->enum('basic', 'premium')
                    ->default('basic')
                    ->comment('User type used in the authorization system');
                $def->index()->uniqueKey('email');
            })->run();

.. code-block:: sql

    CREATE TABLE `Users` (
      `id` int(11) NOT NULL PRIMARY KEY,
      `email` varchar(255) NOT NULL,
      `name` varchar(32) NULL,
      `type` enum('basic', 'premium') NOT NULL DEFAULT 'basic' COMMENT 'User type used in the authorization system',
      UNIQUE KEY (`email`)
    )

ALTER TABLE
^^^^^^^^^^^

.. code-block:: php

    use Framework\Database\Definition\Table\TableDefinition;

    $database->alterTable('Users')
             ->add(function (TableDefinition $def) {
                $def->column('configs')->json()->default('{}');
                $def->column('birthday')->date()->null()->after('name');
             })->run();

.. code-block:: sql

    ALTER TABLE `Users`
      ADD COLUMN `configs` json NOT NULL DEFAULT '{}',
      ADD COLUMN `birthday` date NULL AFTER `name`

DROP TABLE
^^^^^^^^^^

.. code-block:: php

    $database->dropTable('Users')->run();

.. code-block:: sql

    DROP TABLE `Users`
