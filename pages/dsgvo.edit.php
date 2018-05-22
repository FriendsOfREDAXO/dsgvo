<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	$start = rex_request('start', 'int');
	// $csrfToken = rex_csrf_token::factory('dsgvo');

	// if (in_array($func, ['setstatus', 'delete']) && !$csrfToken->isValid()) {
	//	echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
	//	$func = '';
	// } else
	if ($func == 'setstatus') {
		$status = (rex_request('oldstatus', 'int') + 1) % 2;
		$msg = $status == 1 ? 'dsgvo_client_status_activate' : 'dsgvo_client_status_deactivate';
		$oid = rex_request("oid", "int");
		$update = rex_sql::factory()->setQuery('UPDATE rex_dsgvo_client SET status = :status WHERE id = :oid',array(':status' => $status, ':oid' => $oid))->execute(array(':status' => $status, ':oid' => $oid));
		if ($update) {
			echo rex_view::success($this->i18n($msg . '_success', $name));
		} else {
			echo rex_view::error($this->i18n($msg . '_error', $name));
		}
		$func = '';
	}

	if ($func == ''|| $func == "entry_delete") {

		if($func == 'entry_delete') {
    		$oid = rex_request('oid', 'int');
			$delete = rex_sql::factory()->setQuery('DELETE FROM rex_dsgvo_client WHERE id = :oid',array(':oid' => $oid));
			echo rex_view::success( $this->i18n('dsgvo_client_text_entry_deleted'));
    	}	

		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."dsgvo_client` ORDER BY `domain`, `lang`, `prio` ASC", 50);
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_client_norows_message'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add','start' => $start]).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-edit"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###', 'start' => $start]);
		
		$list->setColumnLabel('category', $this->i18n('dsgvo_client_text_column_category'));
		$list->setColumnLabel('domain', $this->i18n('dsgvo_client_text_column_domain'));
		$list->setColumnLabel('lang', $this->i18n('dsgvo_client_text_column_lang'));
		$list->setColumnLabel('name', $this->i18n('dsgvo_client_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_client_text_column_source'));
		$list->setColumnLabel('prio', $this->i18n('dsgvo_client_text_column_prio'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_client_text_column_status'));
		$list->setColumnParams('status', ['func' => 'setstatus', 'oldstatus' => '###status###', 'oid' => '###id###', 'start' => $start]);
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
	    $list->setColumnLabel('updatedate', $this->i18n('dsgvo_client_text_column_updatedate'));
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit', 'start' => $start]);

		$list->addColumn('entry_delete', '<i class="rex-icon rex-icon-delete"></i> ' . $this->i18n('dsgvo_client_text_column_delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    	$list->setColumnParams('entry_delete', ['func' => 'entry_delete', 'oid' => '###id###', 'start' => $start]);
    	$list->addLinkAttribute('entry_delete', 'data-confirm', $this->i18n('dsgvo_server_domain_delete_confirm'));

		$list->removeColumn('keyword');
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('source_url');
		$list->removeColumn('code');
		
		$content = $list->get();
		
		 $fragment = new rex_fragment();
		 $fragment->setVar('class', "info", false);
		 $fragment->setVar('title', $this->i18n('dsgvo_client_text_title'), false);
		 $fragment->setVar('content', $content, false);
		 $content = $fragment->parse('core/page/section.php');
		
		echo $content;

		// Hinweis auf Server-Plugin

		
		$content = '';
		$searchtext = 'rex_cronjob_dsgvo_privacy';
	
		$gm = rex_sql::factory();
		$cronjobs = $gm->setQuery('select * from '.rex::getTable('cronjob').' where type = "rex_cronjob_dsgvo_privacy"')->getArray();
	
		$content .= '<p>'.$this->i18n('check_dsgvo_cronjob_description').'</p>';
	
		if (!count($cronjobs)) {
			$content .= '<p><a class="btn btn-primary" href="index.php?page=cronjob/cronjobs&amp;func=add&amp;list=cronjobs=1" class="rex-button">' . $this->i18n('dsgvo_add_cronjob', htmlspecialchars($module_name)) . '</a>';
		} else {
			$content .= '<p><a class="btn btn-primary" href="index.php?page=cronjob/cronjobs&amp;func=edit&amp;oid='.$cronjobs[0]['id'].'&amp;list=cronjobs" class="rex-button">' . $this->i18n('dsgvo_edit_cronjob', $dsgvo_module_name) . '</a>';
		}
	
		$fragment = new rex_fragment();
		$fragment->setVar('title', $this->i18n('check_dsgvo_cronjob'), false);
		$fragment->setVar('body', $content, false);
		echo $fragment->parse('core/page/section.php');


	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');

		if ($func == 'edit') {
			$formLabel = $this->i18n('dsgvo_client_text_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('dsgvo_client_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_client', '', 'id='.$id);
        $form->addParam('start', $start);

		//Start - add keyword-field
			$field = $form->addTextField('keyword');
			$field->setLabel($this->i18n('dsgvo_client_text_column_keyword'));
			$field->setNotice($this->i18n('dsgvo_client_text_column_keyword_note'));
		//End - add keyword-field

		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('dsgvo_client_text_column_name'));
			$field->setNotice($this->i18n('dsgvo_client_text_column_name_note'));
		//End - add name-field

		//Start - add domain-field
			$field = $form->addTextField('domain');
			$field->setLabel($this->i18n('dsgvo_client_text_column_domain'));
			$field->setNotice($this->i18n('dsgvo_client_text_column_domain_note'));
		//End - add domain-field

		//Start - add lang-field
			$field = $form->addTextField('lang');
			$field->setLabel($this->i18n('dsgvo_client_text_column_lang'));
			$field->setNotice($this->i18n('dsgvo_client_text_column_lang_note'));
		//End - add lang-field

		//Start - add prio-field
			$field = $form->addPrioField('prio');
			$field->setLabel($this->i18n('dsgvo_client_text_column_prio'));
			$field->setLabelField('CONCAT(name, " ", domain)');
			$field->setAttribute('class', 'selectpicker form-control');
			$field->setNotice($this->i18n('dsgvo_client_text_column_prio_note'));
		//End - add prio-field
		
		//Start - add status-field 
			$field = $form->addSelectField('status');
		    $field->setLabel($this->i18n('dsgvo_client_text_column_status'));
		    $select = $field->getSelect();
		    $select->setSize(1);
		    $select->addOption($this->i18n('dsgvo_client_text_column_status_is_online'), 1);
		    $select->addOption($this->i18n('dsgvo_client_text_column_status_is_offline'), 0);
		    if ($func == 'add') {
		        $select->setSelected(1);
		    }		
		    $field->setNotice($this->i18n('dsgvo_client_text_column_status_note'));
		//End - add status-field

		//Start - add text-field
			$field = $form->addTextAreaField('text');
			$field->setLabel($this->i18n('dsgvo_client_text_column_text'));
			$field->setAttribute('class', 'form-control markitupEditor-textile_dsgvo');
			$field->setNotice($this->i18n('dsgvo_client_text_column_text_note'));
		//End - add text-field
		
		//Start - add code-field
			$field = $form->addTextAreaField('code');
			$field->setLabel($this->i18n('dsgvo_client_text_column_code'));
			$field->setAttribute('class', 'form-control codemirror');
			$field->setNotice($this->i18n('dsgvo_client_text_column_code_note'));
		//End - add code-field
		
		//Start - add source-field
			$field = $form->addTextField('source');
			$field->setLabel($this->i18n('dsgvo_client_text_column_source'));
			$field->setNotice($this->i18n('dsgvo_client_text_column_source_note'));
		//End - add source-field

		//Start - add source_url-field
			$field = $form->addTextField('source_url');
			$field->setLabel($this->i18n('dsgvo_client_text_column_source_url'));
			$field->setNotice($this->i18n('dsgvo_client_text_column_source_url_note'));
		//End - add source_url-field

		if ($func == 'edit') {
			$form->addParam('id', $id);
		}
		
		$content = $form->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	}
?>