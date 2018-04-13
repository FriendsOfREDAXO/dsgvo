<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
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

	if ($func == '') {

		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."dsgvo_client` ORDER BY `prio` ASC", 20);
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('sets_norowsmessage'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-edit"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnLabel('domain', $this->i18n('dsgvo_client_text_column_domain'));
		$list->setColumnLabel('lang', $this->i18n('dsgvo_client_text_column_lang'));
		$list->setColumnLabel('name', $this->i18n('dsgvo_client_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_client_text_column_source'));
		$list->setColumnLabel('prio', $this->i18n('dsgvo_server_text_column_prio'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_client_text_column_status'));
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
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);
		
		$list->removeColumn('keyword');
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('custom_text');
		$list->removeColumn('source_url');
		$list->removeColumn('code');
		
		$content = $list->get();
		
		 $fragment = new rex_fragment();
		 $fragment->setVar('class', "info", false);
		 $fragment->setVar('title', $this->i18n('dsgvo_client_text_title'), false);
		 $fragment->setVar('content', $content, false);
		 $content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('dsgvo_client_text_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('dsgvo_client_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_client', '', 'id='.$id);

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
			$field = $form->addSelectField('lang');
			$field->setLabel($this->i18n('dsgvo_client_text_column_lang'));
			$select = $field->getSelect();
		    $select->setSize(1);
		    $select->addOption($this->i18n('dsgvo_client_text_column_lang_is_german'), 'de');
		    $select->addOption($this->i18n('dsgvo_client_text_column_lang_is_english'), 'en');
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
			$field->setAttribute('class', 'form-control markitupEditor-textile_full');
			$field->setNotice($this->i18n('dsgvo_client_text_column_text_note'));
		//End - add text-field
		
		//Start - add custom_text-field
			$field = $form->addTextAreaField('custom_text');
			$field->setLabel($this->i18n('dsgvo_client_text_column_custom_text'));
			$field->setAttribute('class', 'form-control markitupEditor-textile_full');
			$field->setNotice($this->i18n('dsgvo_client_text_column_custom_text_note'));
		//End - add custom_text-field

		//Start - add custom_text-field
			$field = $form->addTextAreaField('code');
			$field->setLabel($this->i18n('dsgvo_client_text_column_code'));
			$field->setAttribute('class', 'form-control codemirror');
			$field->setNotice($this->i18n('dsgvo_client_text_column_code_note'));
		//End - add custom_text-field
		
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