UPGRADE
=======

- [2.0.0](#2.0.0) 
- [1.2.0](#1.2.0)
- [1.1.0](#1.1.0)
- [0.4.0](#0.4.0)

### 2.0.0

For the utf8mb4 compatibility with mysql some fields need to be shorten:

```bash
bin/console doctrine:schema:update
```

### 1.2.0

In the database table `ta_task_executions` a new field was introduced. Run following
command to update the table.

```bash
bin/console doctrine:schema:update
```

### 1.1.0

In the database table `ta_tasks` a new field was introduced. Run following
command to update the table.

```bash
bin/console doctrine:schema:update
```

### 0.4.0

#### Identifier of tasks and executions

The `id` field has been removed in favour of the `uuid`. This field is of
type `guid` and can be used before flush.

To upgrade your table-structure follow following steps:

* Copy the output of `bin/console doctrine:schema:update --dump-sql`.
  Its the direct SQL queries to update your schema.
* Connect to mysql (with command shell or your favourite client
* Disable foreign keys checking by running this query: 
  `set foreign_key_checks=0;`
* Run the queries from `doctrine:schema:update`
* Enable back foreign key checking with : `set foreign_key_checks=1;`
