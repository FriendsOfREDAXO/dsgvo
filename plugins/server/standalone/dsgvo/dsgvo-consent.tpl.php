<? if($_COOKIE['dsgvo_cookie_consent'] != 1) { ?>
<div class="dsgvo-cookie_consent">
	<div class="dsgvo-cookie_consent-inner">
		<div class="dsgvo-cookie_consent-info">
			<p class="dsgvo-cookie_consent-text"><?= $this->consent['info']; ?> <a class="dsgvo-cookie_consent-more" href="<?= $this->consent['url']; ?>"><?= $this->consent['more']; ?></a></p>
		</div>
		<div class="dsgvo-cookie_consent-dismiss">
			<button class="dsgvo-cookie_consent-ok" onClick="dsgvoConsent(1);"><?= $this->consent['dismiss']; ?></button>
		</div>
	</div>
<script>
DsgvoCookies = Cookies.noConflict();

function dsgvoConsent(status) {
	if(status == 1) {
		DsgvoCookies.set("dsgvo_cookie_consent", 1, { expires: 365 });
		document.querySelectorAll('.dsgvo-cookie_consent')[0].style.display = 'none';
	} else {
		DsgvoCookies.set("dsgvo_cookie_consent", -1, { expires: 365 });
	}
	}
</script>
<?= file_get_contents(dirname(__FILE__)."/dsgvo-consent.css") ?>
<? } ?>