<?php

define('PATH', __DIR__ . DIRECTORY_SEPARATOR);

require PATH . 'phproofreading.class.php';

$phproofreading = new PHProofReading();
$languages = $phproofreading->languages();

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>PHProofreading</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<style type="text/css">
	h1 { display: block; padding-top: 20px; }
	h1 span:nth-child(1) { color: #4f5b93; }
	h1 span:nth-child(2) { color: #8892be; }
	h1 span:nth-child(3) { color: #fcfcfc; text-shadow: 0 0 1px #000; }
	mark { cursor: pointer; }
	.tooltip-inner { text-align: inherit; }
	.replacements .btn { margin-bottom: 5px; }
	div.advanced { display: none; }
	</style>
</head>
<body>
	<div class="container">
		<header>
			<h1><span>PH</span><span>P</span><span>roofreading</span></h1>
			<hr>
		</header>

		<form method="post">
			<div class="form-group row">
				<label for="language" class="col-sm-2 col-form-label">Language</label>
				<div class="col-sm-10">
					<select id="language" name="language" class="form-control">
						<option value="auto">(Auto)</option>
						<?php foreach ($languages as $value) { ?>
						<option value="<?=$value['longCode']?>"<?php if (isset($_POST['language']) && $_POST['language'] == $value['longCode']) print ' selected'; ?>><?=$value['name']?> [<?=$value['longCode']?>]</option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label for="text" class="col-sm-2 col-form-label">Text</label>
				<div class="col-sm-10">
					<textarea id="text" name="text" class="form-control" rows="5"><?php if (isset($_POST['text'])) print htmlspecialchars($_POST['text']); ?></textarea>
				</div>
			</div>
			<div class="form-group row">
				<div class="offset-sm-2 col-sm-10">
					<a href="#" class="advanced">Advanced options</a>
					<div class="advanced">
						<label><input name="auto_fix" type="checkbox" value="1"<?php if (isset($_POST['auto_fix'])) print ' checked'; ?>> Automatically fix with first replacement</label>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<div class="offset-sm-2 col-sm-10">
					<button type="submit" class="btn btn-primary">Check Text</button>
				</div>
			</div>
		</form>

		<?php

		if (isset($_POST['language']) && isset($_POST['text'])) {
			$text = trim($_POST['text']);
			$check = $phproofreading->check($text, $_POST['language']);
			$count = count($check['matches']);

		?>
		<div class="row">
			<div class="offset-sm-2 col-sm-10">
				<?php if (isset($_POST['language']) && $_POST['language'] == 'auto') { ?>
				<div class="alert alert-info"><?=$check['language']['name']?> [<?=$check['language']['code']?>] detected.</div>
				<?php } ?>

				<?php if ($count < 1) { ?>
				<div class="alert alert-success">No errors were found.</div>
				<?php } else { ?>

				<?php if (isset($_POST['auto_fix'])) { ?>
				<div class="alert alert-success"><?=$count?> error<?php if ($count > 1) print 's'; ?> fixed.</div>
				<?php } else { ?>
				<div class="alert alert-warning"><?=$count?> error<?php if ($count > 1) print 's'; ?> found.</div>
				<?php } ?>

				<div class="card bg-light text-dark">
					<div class="card-block">
						<?php

						krsort($check['matches']);
						mb_internal_encoding('UTF-8');

						foreach ($check['matches'] as $key => $match) {
							if (isset($_POST['auto_fix']) && count($match['replacements']) > 0) {
								$text = mb_substr($text, 0, $match['offset']) . '<em>' . $match['replacements'][0]['value'] . '</em>' . mb_substr($text, $match['offset'] + $match['length']);
							} else {
								$tooltip = '<p>' . $match['message'] . '</p><div class="replacements">';

								foreach ($match['replacements'] as $replacement)
									$tooltip .= '<button type="button" class="btn-correct btn btn-sm btn-success" data-mark="' . $key . '">' . $replacement['value'] . '</button> ';

								$tooltip .= '</div>';

								$text = mb_substr($text, 0, $match['offset']) . '<mark id="mark_' . $key . '" data-toggle="tooltip" data-html="true" title="' . htmlspecialchars($tooltip) . '">' . mb_substr($text, $match['offset'], $match['length']) . '</mark>' . mb_substr($text, $match['offset'] + $match['length']);
							}
						}

						print nl2br($text);

						?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

		<footer>
			<hr>
			<div class="float-left">Powered by <a href="https://languagetool.org/" target="_blank">LanguageTool</a> API</div>
			<div class="float-right text-center"><a href="https://github.com/hamidsamak/phproofreading" target="_blank">PHProofreading</a><br>by <a href="https://github.com/hamidsamak" target="_blank">Hamid Samak</a></div>
		</footer>
	</div>

	<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	<script type="text/javascript">
	$(function () {
		$('[data-toggle="tooltip"]').tooltip( { trigger: "click focus" } );

		$("mark").click(function(){
			$("mark").not(this).tooltip("hide");
		});

		$("body").on("click", ".btn-correct", function(){
			var mark_id = $(this).attr("data-mark").toString();
			var mark = $("#mark_" + mark_id);

			mark.html($(this).html());
			mark.tooltip("dispose");
			mark.replaceWith(mark.html());
		});

		$("body").on("click", function(){
			$('[data-toggle="tooltip"]').tooltip("hide");
		});

		$("a.advanced").click(function(event){
			event.preventDefault();

			$(this).hide().next().show();
		});

		<?php if (isset($_POST['auto_fix'])) { ?>
		$("a.advanced").click();
		<?php } ?>
	});
	</script>
</body>
</html>