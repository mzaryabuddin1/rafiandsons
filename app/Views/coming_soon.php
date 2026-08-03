<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
	<title>Rafi &amp; Sons — Coming Soon</title>
	<meta name="description" content="Rafi & Sons is almost here. Our new storefront is on the way.">
	<link rel="icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
	<script>
		WebFontConfig = {
			google: { families: ['Poppins:400,500,600,700,800'] }
		};
		(function (d) {
			var wf = d.createElement('script'), s = d.scripts[0];
			wf.src = '<?= base_url('theme/js/webfont.js') ?>';
			wf.async = true;
			s.parentNode.insertBefore(wf, s);
		})(document);
	</script>
	<link rel="stylesheet" type="text/css" href="<?= base_url('theme/css/demo22.min.css') ?>">
	<style>
		:root {
			--rs-primary: #05b895;
			--rs-ink: #222222;
			--rs-body: #666666;
			--rs-muted: #999999;
			--rs-surface: #ffffff;
		}

		.coming-soon-page {
			min-height: 100vh;
			min-height: 100dvh;
			display: flex;
			flex-direction: column;
			background:
				radial-gradient(ellipse 70% 50% at 80% 10%, rgba(5, 184, 149, 0.12), transparent 55%),
				radial-gradient(ellipse 60% 45% at 10% 90%, rgba(5, 184, 149, 0.08), transparent 50%),
				var(--rs-surface);
			font-family: Poppins, sans-serif;
			overflow: hidden;
		}

		.coming-soon-page .cs-main {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 4rem 0 5rem;
			text-align: center;
		}

		.coming-soon-page .cs-brand {
			display: inline-block;
			margin-bottom: 2.4rem;
			opacity: 0;
			animation:
				cs-rise 0.9s ease 0.1s forwards,
				cs-float 5.5s ease-in-out 1.1s infinite;
		}

		.coming-soon-page .cs-brand img {
			width: min(220px, 58vw);
			height: auto;
			display: block;
			filter: drop-shadow(0 12px 28px rgba(5, 184, 149, 0.18));
		}

		.coming-soon-page .cs-title {
			margin-bottom: 1.2rem;
			color: var(--rs-ink);
			font-size: clamp(3.2rem, 6vw, 4.8rem);
			font-weight: 700;
			letter-spacing: -0.03em;
			line-height: 1.15;
			opacity: 0;
			animation: cs-rise 0.9s ease 0.35s forwards;
		}

		.coming-soon-page .cs-title span {
			color: var(--rs-primary);
			display: inline-block;
			animation: cs-glow 2.8s ease-in-out 1.2s infinite;
		}

		.coming-soon-page .cs-rule {
			width: 0;
			height: 2px;
			margin: 0 auto 1.8rem;
			background: linear-gradient(90deg, transparent, var(--rs-primary), transparent);
			animation: cs-draw 1s ease 0.7s forwards;
		}

		.coming-soon-page .cs-text {
			max-width: 52rem;
			margin: 0 auto;
			color: var(--rs-body);
			font-size: 1.5rem;
			line-height: 1.7;
			opacity: 0;
			animation: cs-rise 0.9s ease 0.6s forwards;
		}

		.coming-soon-page .cs-footer {
			padding: 2rem 0;
			background: var(--rs-ink);
			color: var(--rs-muted);
			font-size: 1.3rem;
			text-align: center;
			opacity: 0;
			animation: cs-fade 0.8s ease 0.95s forwards;
		}

		.coming-soon-page .cs-footer a {
			color: #fff;
		}

		.coming-soon-page .cs-footer a:hover {
			color: var(--rs-primary);
		}

		@keyframes cs-rise {
			from {
				opacity: 0;
				transform: translateY(22px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		@keyframes cs-fade {
			from { opacity: 0; }
			to { opacity: 1; }
		}

		@keyframes cs-draw {
			to { width: min(12rem, 40vw); }
		}

		@keyframes cs-float {
			0%, 100% { transform: translateY(0); }
			50% { transform: translateY(-10px); }
		}

		@keyframes cs-glow {
			0%, 100% { text-shadow: 0 0 0 transparent; }
			50% { text-shadow: 0 0 18px rgba(5, 184, 149, 0.35); }
		}

		@media (prefers-reduced-motion: reduce) {
			.coming-soon-page .cs-brand,
			.coming-soon-page .cs-title,
			.coming-soon-page .cs-title span,
			.coming-soon-page .cs-text,
			.coming-soon-page .cs-footer,
			.coming-soon-page .cs-rule {
				animation: none !important;
				opacity: 1;
				transform: none;
			}

			.coming-soon-page .cs-rule {
				width: min(12rem, 40vw);
			}
		}
	</style>
</head>
<body>
	<div class="page-wrapper coming-soon-page">
		<main class="cs-main">
			<div class="container">
				<a href="<?= site_url('/') ?>" class="cs-brand" aria-label="Rafi &amp; Sons">
					<img src="<?= base_url('assets/images/logo.png') ?>" alt="Rafi &amp; Sons — Dream's of Life" width="220" height="220">
				</a>
				<h1 class="cs-title">We&rsquo;re preparing something <span>special</span></h1>
				<div class="cs-rule" aria-hidden="true"></div>
				<p class="cs-text">
					Our new storefront is almost ready. Check back shortly for curated pieces,
					thoughtful service, and a shopping experience built around you.
				</p>
			</div>
		</main>

		<footer class="cs-footer">
			<div class="container">
				<p class="mb-0">&copy; <?= date('Y') ?> <a href="<?= site_url('/') ?>">Rafi &amp; Sons</a>. All rights reserved.</p>
			</div>
		</footer>
	</div>
</body>
</html>
