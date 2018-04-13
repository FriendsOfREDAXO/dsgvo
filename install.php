<?php

rex_sql_table::get(rex::getTable('dsgvo_client'))
->ensureColumn(new rex_sql_column('id', 'int(11)', false, null, 'auto_increment'))
->ensureColumn(new rex_sql_column('keyword', 'text'))
->ensureColumn(new rex_sql_column('name', 'text'))
->ensureColumn(new rex_sql_column('text', 'text'))
->ensureColumn(new rex_sql_column('custom_text', 'text'))
->ensureColumn(new rex_sql_column('source', 'text'))
->ensureColumn(new rex_sql_column('source_url', 'text'))
->ensureColumn(new rex_sql_column('status', 'text'))
->ensureColumn(new rex_sql_column('prio', 'int(11)'))
->ensureColumn(new rex_sql_column('code', 'text'))
->ensureColumn(new rex_sql_column('lang', 'text'))
->setPrimaryKey('id')
// ->ensureIndex(new rex_sql_index('keyword', ['keyword'], rex_sql_index::UNIQUE))
->ensure();