<? if($_COOKIE['dsgvo_cookie_consent'] != 1) { ?>
<div class="dsgvo-cookie_consent uk-alert uk-animation-slide-bottom uk-margin-remove" style="position: fixed; right: 0; left: 0; bottom: 0;">
	<div class="dsgvo-cookie_consent-inner uk-padding-small" style="display: flex; align-items: center;">
		<div class="dsgvo-cookie_consent-info" style="flex: 1 1 auto; margin-right: 10px;">
			<p class=""><?= $this->info; ?><a class="uk-button uk-button-text" href="<?= $this->url; ?>"><?= $this->learn_more; ?></a></p>
</p>		</div>
		<div class="dsgvo-cookie_consent-dismiss">
		<button class="uk-button uk-button-default" onClick="dsgvoConsent(1);"><?= $this->dismiss; ?></button>
	</div>
</div>
<script>
function dsgvoConsent(status) {
	if(status == 1) {
		Cookies.set("dsgvo_cookie_consent", 1, { expires: 365 });
		document.querySelectorAll('.dsgvo-cookie_consent')[0].style.display = 'none';
	} else {
		Cookies.set("dsgvo_cookie_consent", -1, { expires: 365 });
	}
}
</script>
<?= rex_config::get("dsgvo", "dsgvo_consent_css"); ?>
<? } ?>