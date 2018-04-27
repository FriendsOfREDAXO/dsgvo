<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	$domain = rex_request('domain', 'string');
	$result = rex_request('result', 'string', false);

	if (($func == '' && !$result) || $func == "domain_delete") { 

		if($func == 'domain_delete') {
    		$oid = rex_request('oid', 'int');
			$delete = rex_sql::factory()->setQuery('DELETE FROM rex_dsgvo_server_project WHERE id = :oid',array(':oid' => $oid));
			$delete = rex_sql::factory()->setDebug(0)->setQuery('DELETE FROM rex_dsgvo_server WHERE domain = :domain',array(':domain' => $domain));
			echo rex_view::success( $this->i18n('dsgvo_server_domain_deleted'));
    	}	

		// Domain-Übersicht ANFANG //
		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."dsgvo_server_project` ORDER BY `domain` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_server_norows_message'));
		
		// icon column (Domain hinzufügen bzw. bearbeiten)
		$thIcon = '<a href="'.$list->getUrl(['func' => 'domain_add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'domain_edit', 'id' => '###id###']);
		
		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_domain_column_domain'));
		$list->setColumnParams('domain', ['id' => '###id###', 'func' => 'domain_edit']);
			
		$list->addColumn($this->i18n('dsgvo_server_domain_column_manage_text'), $this->i18n('dsgvo_server_domain_column_manage_text'), 3);
		$list->setColumnParams($this->i18n('dsgvo_server_domain_column_manage_text'), ['data_id' => '###id###', 'func' => 'domain_details', 'domain' => '###domain###']);

		$list->setColumnLabel('api_key', $this->i18n('dsgvo_server_domain_column_api_key'));

		$list->addColumn('last_call', '');
		$list->setColumnLabel('last_call', $this->i18n('dsgvo_server_domain_column_last_call'));
		$list->setColumnFormat('last_call', 'custom', function ($params) {
			$last_call = array_shift(array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$params['list']->getValue('domain').'" ORDER BY createdate DESC')));
			if ($last_call) {
				return $last_call['createdate'];
			} else { 
				return rex_i18n::msg("dsgvo_server_domain_column_last_call_none");
			}
		});
		
		$list->addColumn('domain_delete', '<i class="rex-icon rex-icon-delete"></i> ' . $this->i18n('dsgvo_server_domain_column_delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    	$list->setColumnParams('domain_delete', ['func' => 'domain_delete', 'oid' => '###id###', 'domain' => '###domain###']);
    	$list->addLinkAttribute('domain_delete', 'data-confirm', $this->i18n('dsgvo_server_domain_delete_confirm'));

    	$list->removeColumn('id');
		$list->removeColumn('updatedate');
		
		$content1 = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_domain_list_title'), false);
		$fragment->setVar('content', $content1, false);
		$content1 = $fragment->parse('core/page/section.php');
		
		echo $content1;
		// Domain-Übersicht ENDE //

	} else if ($func == 'domain_add' || $func == 'domain_edit') { 
		
		// Domain bearbeiten ANFANG //

		$id = rex_request('id', 'int');
		
		if ($func == 'domain_edit') {
			$formLabel = $this->i18n('dsgvo_server_text_edit');
		} elseif ($func == 'domain_add') {
			$formLabel = $this->i18n('dsgvo_server_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_server_project', '', 'id='.$id);
		
		//Start - add domain-field
		$field = $form->addTextField('domain');
		$field->setLabel($this->i18n('dsgvo_server_domain_column_domain'));
		$field->setNotice($this->i18n('dsgvo_server_domain_column_domain_note'));
		//End - add domain-field

		//Start - add domain-field
		$field = $form->addTextField('api_key');
		$field->setLabel($this->i18n('dsgvo_server_domain_column_api_key'));
		$field->setNotice($this->i18n('dsgvo_server_domain_column_api_key_note', md5(time())));
		//End - add domain-field
		
		if ($func == 'domain_edit') {
			$form->addParam('id', $id);
		}

		$content3 = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content3, false);
		$content3 = $fragment->parse('core/page/section.php');

		echo $content3;
		// Domain bearbeiten ENDE //

	} else if ($func == 'domain_details' || $func == 'text_copy_default_de' || $func == 'text_copy_default_en'|| $func == 'text_delete' || $func == 'set_text_status' || ($func == '' && $result)) {

		if($func == 'text_delete') {
    		$oid = rex_request('oid', 'int');
			$delete = rex_sql::factory()->setQuery('DELETE FROM rex_dsgvo_server WHERE id = :oid',array(':oid' => $oid));
			echo rex_view::success( $this->i18n('dsgvo_server_text_deleted'));
		}	
		if($func == 'text_copy_default_de') {
			$query = 'INSERT INTO rex_dsgvo_server (`domain`, `lang`, `name`, `keyword`, `text`, `source`, `source_url`, `prio`, `status`, `updatedate`) (SELECT :domain AS `domain`, `lang`, `name`, `keyword`, `text`, `source`, `source_url`, `prio`, 0 as `status`, NOW() FROM rex_dsgvo_server WHERE domain = "default" AND lang = "de")';
			rex_sql::factory()->setDebug(0)->setQuery($query, [":domain" => $domain]);
			echo rex_view::success( $this->i18n('dsgvo_server_text_default_copied'));
		}	
		if($func == 'text_copy_default_en') {
			$query = 'INSERT INTO rex_dsgvo_server (`domain`, `lang`, `name`, `keyword`, `text`, `source`, `source_url`, `prio`, `status`, `updatedate`) (SELECT :domain AS `domain`, `lang`, `name`, `keyword`, `text`, `source`, `source_url`, `prio`, 0 as `status`, NOW() FROM rex_dsgvo_server WHERE domain = "default" AND lang = "en")';
			rex_sql::factory()->setDebug(0)->setQuery($query, [":domain" => $domain]);
			echo rex_view::success( $this->i18n('dsgvo_server_text_default_copied'));
		}	
				
		// Offline / Online schalten
		if ($func == 'set_text_status') {
			$status = (rex_request('oldstatus', 'int') + 1) % 2;
			$msg = $status == 1 ? 'dsgvo_server_status_activate' : 'dsgvo_server_status_deactivate';
			$oid = rex_request("oid", "int");
			$update = rex_sql::factory()->setQuery('UPDATE rex_dsgvo_server SET status = :status WHERE id = :oid',array(':status' => $status, ':oid' => $oid))->execute(array(':status' => $status, ':oid' => $oid));
			if ($update) {
				echo rex_view::success($this->i18n($msg . '_success', $name));
			} else {
				echo rex_view::error($this->i18n($msg . '_error', $name));
			}
		}
		

		$list = rex_list::factory('SELECT * FROM `'.rex::getTablePrefix().'dsgvo_server` WHERE domain = "'.$domain.'" ORDER BY `prio` ASC');
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_server_norows_message'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'text_add', 'domain' => $domain]).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'text_edit', 'domain' => $domain, 'id' => '###id###']);
		
		//$list->setColumnLabel('name', $this->i18n('sets_column_name'));
		$list->setColumnLabel('type', $this->i18n('sets_column_type'));		
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'text_edit', 'domain' => $domain]);

		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_text_column_domain'));
		
		$list->setColumnLabel('lang', $this->i18n('dsgvo_server_text_column_lang'));
		$list->setColumnLabel('name', $this->i18n('dsgvo_server_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_server_text_column_source'));
		$list->setColumnLabel('prio', $this->i18n('dsgvo_server_text_column_prio'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_server_text_column_status'));
		$list->setColumnParams('status', ['func' => 'set_text_status', 'oldstatus' => '###status###', 'oid' => '###id###', 'domain' => '###domain###']);
		$list->setColumnFormat('status', 'custom', function ($params) {
			$list = $params['list'];  
			if ($params['value'] == "") {
				$str = rex_i18n::msg('dsgvo_client_text_column_status_invalid');
			} elseif ($params['value'] == 1) {
				$str = $list->getColumnLink('status', '<span class="rex-online"><i class="rex-icon rex-icon-online"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_online') . '</span>');
			} else {
				$str = $list->getColumnLink('status', '<span class="rex-offline"><i class="rex-icon rex-icon-offline"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_offline') . '</span>');
			}
			return $str;
		});
		$list->setColumnLabel('updatedate', $this->i18n('dsgvo_server_text_column_updatedate'));

		$list->addColumn('text_delete', '<i class="rex-icon rex-icon-delete"></i> ' . $this->i18n('dsgvo_server_text_column_delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    	$list->setColumnParams('text_delete', ['func' => 'text_delete', 'oid' => '###id###', 'domain' => '###domain###']);
    	$list->addLinkAttribute('text_delete', 'data-confirm', $this->i18n('dsgvo_server_text_delete_confirm'));
		
		$list->removeColumn('keyword');
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('custom_text');
		$list->removeColumn('source_url');
		$list->removeColumn('code');
		
		$content4 = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_text_title'), false);
		$fragment->setVar('content', $content4, false);
		$content4 = $fragment->parse('core/page/section.php');
		
		echo $content4;

		// LOGS
		$domain = rex_request('domain', 'string', "");
		$list = rex_list::factory('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$domain.'" ORDER BY createdate DESC LIMIT 30', 10, "domain");

		$list->setColumnLabel('id', $this->i18n('dsgvo_server_project_log_id'));
		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_project_log_domain'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_server_project_log_status'));
		$list->setColumnLabel('createdate', $this->i18n('dsgvo_server_project_log_createdate'));
		$list->removeColumn('raw');
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'default', false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_project_log_title'), false);
		$fragment->setVar('body', $list->get(), false);
		$content5 = $fragment->parse('core/page/section.php');
		echo $content5;
		// LOGS END


		if(rex_request('domain','string') != 'default') {
			$buttons_de = '<a class="btn btn-edit" href="index.php?page=dsgvo/server-edit&func=text_copy_default_de&domain='.$domain.'">' . rex_i18n::msg('dsgvo_server_default_text_copy_de') . '</a>';
			$buttons_en = '<a class="btn btn-edit" href="index.php?page=dsgvo/server-edit&func=text_copy_default_en&domain='.$domain.'">' . rex_i18n::msg('dsgvo_server_default_text_copy_en') . '</a>';

			$fragment = new rex_fragment();
			$fragment->setVar('class', 'default', false);
			$fragment->setVar('title', $this->i18n('dsgvo_server_default_title'), false);
			$fragment->setVar('body', $buttons_de." ".$buttons_en, false);
			echo $fragment->parse('core/page/section.php');
		}


	} else if ($func == 'text_edit' || $func == 'text_add') { // Wenn von einer Domain Texte verwaltet werden
				
		// Text bearbeiten ANFANG //
		$id = rex_request('id', 'int');
		
		if ($func == 'text_edit') {
			$formLabel = $this->i18n('dsgvo_server_text_edit');
		} elseif ($func == 'text_add') {
			$formLabel = $this->i18n('dsgvo_server_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_server', '', 'id='.$id);

		//Start - add status-field 
		$field = $form->addSelectField('category');
		$field->setLabel($this->i18n('dsgvo_server_text_column_category'));
		$select = $field->getSelect();
		$select->setSize(1);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_1'), 1);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_2'), 2);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_3'), 3);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_4'), 4);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_5'), 5);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_6'), 6);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_7'), 7);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_8'), 8);
		$field->setNotice($this->i18n('dsgvo_server_text_column_status_note'));
		//End - add status-field

		//Start - add keyword-field
			$field = $form->addTextField('keyword');
			$field->setLabel($this->i18n('dsgvo_server_text_column_keyword'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_keyword_note'));
		//End - add keyword-field
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('dsgvo_server_text_column_name'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_name_note'));
		//End - add name-field

		//Start - add domain-field
			$field = $form->addSelectField('domain','',['class'=>'form-control selectpicker']); 
			$field->setLabel($this->i18n('dsgvo_server_text_column_domain'));
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addDBSqlOptions("select domain as name, domain as id FROM rex_dsgvo_server_project ORDER BY domain");
			$select->setSelected($domain);
			$field->setNotice($this->i18n('dsgvo_server_text_column_domain_note'));
		//End - add domain-field

		//Start - add lang-field
			$field = $form->addTextField('lang');
			$field->setLabel($this->i18n('dsgvo_server_text_column_lang'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_lang_note'));
		//End - add lang-field
		
		//Start - add text-field
			$field = $form->addTextAreaField('text');
			$field->setLabel($this->i18n('dsgvo_server_text_column_text'));
			$field->setAttribute('class', 'form-control markitupEditor-textile_dsgvo');
			$field->setNotice($this->i18n('dsgvo_server_text_column_text_note'));
		//End - add text-field

		//Start - add code-field
			$field = $form->addTextAreaField('code');
			$field->setLabel($this->i18n('dsgvo_server_text_column_code'));
			$field->setAttribute('class', 'codemirror form-control');
			$field->setNotice($this->i18n('dsgvo_server_text_column_code_note'));
		//End - add text-field
		
		//Start - add source-field
			$field = $form->addTextField('source');
			$field->setLabel($this->i18n('dsgvo_server_text_column_source'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_source_note'));
		//End - add source-field
		
		//Start - add source_url-field
			$field = $form->addTextField('source_url');
			$field->setLabel($this->i18n('dsgvo_server_text_column_source_url'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_source_url_note'));
		//End - add source_url-field

		//Start - add prio-field
			$field = $form->addPrioField('prio');
			$field->setLabel($this->i18n('dsgvo_server_text_column_prio'));
			$field->setLabelField('CONCAT(name, " (", domain,"-" ,lang, ")")');
			$field->setAttribute('class', 'selectpicker form-control');
			$field->setWhereCondition('domain = "'.$domain.'"');
			$field->setNotice($this->i18n('dsgvo_server_text_column_prio_note'));
		//End - add prio-field

		//Start - add status-field 
			$field = $form->addSelectField('status');
			$field->setLabel($this->i18n('dsgvo_server_text_column_status'));
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption($this->i18n('dsgvo_server_text_column_status_is_online'), 1);
			$select->addOption($this->i18n('dsgvo_server_text_column_status_is_offline'), 0);
			if ($func == 'text_add') {
				$select->setSelected(1);
			}		
			$field->setNotice($this->i18n('dsgvo_server_text_column_status_note'));
		//End - add status-field
		
		if ($func == 'text_edit') {
			$form->addParam('id', $id);
		}

		$form->addParam("domain", $domain);
		$form->addParam("result", "text_added");

		$content2 = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content2, false);
		$content2 = $fragment->parse('core/page/section.php');
		
		echo $content2;
		// Text bearbeiten ENDE //

	}  
?>