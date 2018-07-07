<?php if($_COOKIE['dsgvo_cookie_consent'] != 1) { ?>
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

			<?php if(isset($this->consent['html_padding'])) { ?>
				document.body.parentNode.style.padding<?= $this->consent['html_padding'] ?> = "0px";
			<?php } ?>

		} else {
			DsgvoCookies.set("dsgvo_cookie_consent", -1, { expires: 365 });
		}
	}

	<?php if(isset($this->consent['html_padding'])) { ?>
		var DsgvoConsentPadding = function (event) {
			document.body.parentNode.style.padding<?= $this->consent['html_padding'] ?> = document.querySelectorAll('.dsgvo-cookie_consent')[0].offsetHeight+"px";
		}
		window.onresize = DsgvoConsentPadding;
		window.onload = DsgvoConsentPadding;
	<?php } ?>

	</script>
	<?= file_get_contents(dirname(__FILE__)."/dsgvo-consent.css") ?>
<?php } ?>