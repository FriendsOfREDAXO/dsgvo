<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	
	if ($func == '') { 

		// Domain-Übersicht ANFANG //

		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."dsgvo_server_project` ORDER BY `domain` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_server_norows_message'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'domain_add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'domain_edit', 'id' => '###id###']);
		
		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_projects_column_domain'));
		$list->setColumnParams('domain', ['id' => '###id###', 'func' => 'domain_edit']);
		$list->setColumnLabel('api_key', $this->i18n('dsgvo_server_projects_column_api_key'));	
		$list->removeColumn('id');
		$list->removeColumn('updatedate');

		
		$list->addColumn($this->i18n('dsgvo_server_projects_column_manage_text'), $this->i18n('dsgvo_server_projects_column_manage_text'));
		$list->setColumnParams($this->i18n('dsgvo_server_projects_column_manage_text'), ['data_id' => '###id###', 'func' => 'domain_details', 'domain' => '###domain###']);

		$list->addColumn('last_call', '');
		$list->setColumnLabel('last_call', $this->i18n('dsgvo_server_projects_column_last_call'));
		$list->setColumnFormat('last_call', 'custom', function ($params) {
			$last_call = array_shift(array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$params['list']->getValue('domain').'" ORDER BY datestamp DESC')));
			if ($last_call) {
				return $last_call['datestamp'];
			}
			return rex_i18n::msg("dsgvo_server_projects_column_last_call_none");
		});
		

		$content1 = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_client_project_server_title'), false);
		$fragment->setVar('content', $content1, false);
		$content1 = $fragment->parse('core/page/section.php');
		
		echo $content1;
		// Domain-Übersicht ENDE //

	} else if ($func == 'text_edit' || $func == 'text_add') { // Wenn von einer Domain Texte verwaltet werden
				
		// Text bearbeiten ANFANG //
		$id = rex_request('id', 'int');
		
		if ($func == 'text_edit') {
			$formLabel = $this->i18n('dsgvo_server_text_edit');
		} elseif ($func == 'text_add') {
			$formLabel = $this->i18n('dsgvo_server_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_server', '', 'id='.$id);

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
			$field->setNotice($this->i18n('dsgvo_server_text_column_domain_note'));
		//End - add domain-field

		//Start - add lang-field
			$field = $form->addSelectField('lang','',['class'=>'form-control selectpicker']);
			$field->setLabel($this->i18n('dsgvo_server_text_column_lang'));
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption($this->i18n('dsgvo_server_text_column_lang_is_german'), "de");
			$select->addOption($this->i18n('dsgvo_server_text_column_lang_is_english'), 'en');
			$field->setNotice($this->i18n('dsgvo_server_text_column_lang_note'));
		//End - add lang-field
		
		//Start - add text-field
			$field = $form->addTextAreaField('text');
			$field->setLabel($this->i18n('dsgvo_server_text_column_text'));
			$field->setAttribute('class', 'form-control markitupEditor-textile_full');
			$field->setNotice($this->i18n('dsgvo_server_text_column_text_note'));
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
			$field->setLabelField('CONCAT(name, " ", domain)');
			$field->setAttribute('class', 'selectpicker form-control');
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
		
		$content2 = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content2, false);
		$content2 = $fragment->parse('core/page/section.php');
		
		echo $content2;
		// Text bearbeiten ENDE //

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
		$field->setLabel($this->i18n('dsgvo_server_projects_column_domain'));
		$field->setNotice($this->i18n('dsgvo_server_projects_column_domain_note'));
		//End - add domain-field

		//Start - add domain-field
		$field = $form->addTextField('api_key');
		$field->setLabel($this->i18n('dsgvo_server_projects_column_api_key'));
		$field->setNotice($this->i18n('dsgvo_server_projects_column_api_key_note'));
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

	} else if ($func == 'domain_details') { 

		// Offline / Online schalten
		$func = rex_request('func', 'string');
		if ($func == 'setstatus') {
			$status = (rex_request('oldstatus', 'int') + 1) % 2;
			$msg = $status == 1 ? 'dsgvo_server_status_activate' : 'dsgvo_server_status_deactivate';
			$oid = rex_request("oid", "int");
			$update = rex_sql::factory()->setQuery('UPDATE rex_dsgvo_server SET status = :status WHERE id = :oid',array(':status' => $status, ':oid' => $oid))->execute(array(':status' => $status, ':oid' => $oid));
			if ($update) {
				echo rex_view::success($this->i18n($msg . '_success', $name));
			} else {
				echo rex_view::error($this->i18n($msg . '_error', $name));
			}
			$func = '';
		}

		$list = rex_list::factory('SELECT * FROM `'.rex::getTablePrefix().'dsgvo_server` WHERE domain = "'.$domain.'" ORDER BY `prio` ASC');
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_server_norows_message'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'text_add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'text_edit', 'id' => '###id###']);
		
		//$list->setColumnLabel('name', $this->i18n('sets_column_name'));
		$list->setColumnLabel('type', $this->i18n('sets_column_type'));		
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'text_edit']);

		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_text_column_domain'));
		$list->setColumnLabel('lang', $this->i18n('dsgvo_server_text_column_lang'));
		$list->setColumnLabel('name', $this->i18n('dsgvo_server_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_server_text_column_source'));
		$list->setColumnLabel('prio', $this->i18n('dsgvo_server_text_column_prio'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_server_text_column_status'));
		$list->setColumnParams('status', ['func' => 'setstatus', 'oldstatus' => '###status###', 'oid' => '###id###']);
		$list->setColumnFormat('status', 'custom', function ($params) {
			$list = $params['list'];  
			if ($params['value'] == "") {
				$str = rex_i18n::msg('cronjob_status_invalid');
			} elseif ($params['value'] == 1) {
				$str = $list->getColumnLink('status', '<span class="rex-online"><i class="rex-icon rex-icon-online"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_online') . '</span>');
			} else {
				$str = $list->getColumnLink('status', '<span class="rex-offline"><i class="rex-icon rex-icon-offline"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_offline') . '</span>');
			}
			return $str;
		});
		$list->setColumnLabel('updatedate', $this->i18n('dsgvo_server_text_column_updatedate'));
		
		$list->removeColumn('keyword');
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('custom_text');
		$list->removeColumn('source_url');
		
		$content4 = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_text_title'), false);
		$fragment->setVar('content', $content4, false);
		$content4 = $fragment->parse('core/page/section.php');
		
		echo $content4;

		// LOGS
		$domain = rex_request('domain', 'string', "");
		$list = rex_list::factory('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$domain.'" ORDER BY datestamp DESC LIMIT 30', 10, "domain");
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'default', false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_project_log_title'), false);
		$fragment->setVar('body', $list->get(), false);
		$content5 = $fragment->parse('core/page/section.php');

		echo $content5;
		// LOGS END

	}
?>