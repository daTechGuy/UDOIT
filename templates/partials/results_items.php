<?php
/**
*	Copyright (C) 2014 University of Central Florida, created by Jacob Bates, Eric Colon, Fenel Joseph, and Emily Sachs.
*
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*	Primary Author Contact:  Jacob Bates <jacob.bates@ucf.edu>
*/
?>
<div class="panel <?= $panel_style ?> no-margin">
	<div class="panel-heading">
		<h4 class="panel-title"><span class="badge"><?= count($items); ?></span> <?= $title ?></h4>
	</div>
	<ul class="list-group">
		<?
			// group the error types together
			$tmp_types = [];
			foreach ($items as $tmp_item) {
				if ( ! isset($tmp_types[$tmp_item->type])) {
					$tmp_types[$tmp_item->type] = [];
				}
				$tmp_types[$tmp_item->type][] = $tmp_item;
			}
		?>
		<? foreach ($tmp_types as $type => $type_group): ?>
			<? $heading_data = $type_group[0]; ?>
			<? $collapse_id = "collapse-{$id}-{$type}"; ?>
			<li class="list-group-item">

				<!-- Error group header -->
				<div class="collapse-header" data-toggle="collapse" data-target="#<?= $collapse_id ?>">
					<h5 class="text-danger title-line">
						<span class="badge badge-error"><?= count($type_group) ?> x </span> <?= $heading_data->title; ?>
					</h5>
				</div>
				<? if (isset($heading_data->description)): ?>
					<div class="error-desc"><p><?= $heading_data->description ?></p></div>
				<? endif; ?>
				<!-- End Error group header -->

				<div id="<?= $collapse_id ?>" class="collapse in fade margin-top-small">
					<ol>
						<? foreach ($type_group as $index => $group_item): ?>
							<? $li_id = "error-{$id}-{$type}-{$index}"; ?>
							<li id="<?= $li_id ?>">

								<? if ( in_array($group_item->type, $fixable_types) ): ?>
									<p class="fix-success hidden"><?= $index; ?>. <span class="label label-success margin-left-small" style="margin-top: -2px;">Done!</span></p>
								<? endif; ?>

								<!-- Print Report -->
								<? if ($group_item->html): ?>
									<a class="viewError btn" href="#viewError" data-error="<?= $li_id ?>">View the source of this issue</a>
									<div class="more-info hidden instance">
										<a class="closeError btn" href="#closeError" data-error="<?= $li_id ?>">Close Issue Source</a>
										<div class="error-preview">
											<? if ($group_item->type == "videosEmbeddedOrLinkedNeedCaptions"): ?>
												<iframe width="100%" height="300px" src="https://www.youtube.com/embed/<?= isYoutubeVideo($group_item->html, $regex); ?>" frameborder="0" allowfullscreen></iframe>
											<? else: ?>
												<?= $group_item->html; ?>
											<? endif; ?>
										</div>
										<pre class="error-source"><code class="html"><strong>Line <?= $group_item->lineNo; ?></strong>: <?= htmlspecialchars($group_item->html); ?></code></pre>
										<a class="closeError btn" href="#closeError" data-error="<?= $li_id ?>">Close Issue Source</a>
									</div>
								<? endif; ?>

								<? if (empty($post_path) && in_array($group_item->type, $fixable_types)): ?>
									<div>
										<button class="fix-this no-print btn btn-success instance" value="<?= $group_item->type ?>">U FIX IT!</button>
										<div class="toolmessage instance">UFIXIT is disabled because this is an old report. Rescan the course to use UFIXIT.</div>
									</div>
									<form class="ufixit-form form-horizontal no-print hidden instance" action="#" role="form">
										<input type="hidden" name="main_action" value="ufixit">
										<input type="hidden" name="contenttype" value="<?= $content_group->title; ?>">
										<input type="hidden" name="contentid" value="<?= $id; ?>">
										<input type="hidden" name="errorhtml" value="<?= htmlspecialchars($group_item->html); ?>">
										<input type="hidden" name="errortype" value="<?= $group_item->type; ?>">
										<input type="hidden" name="reporttype" value="error">
										<input type="hidden" name="reporttype" value="suggestion">
										<input type="hidden" name="errortype" value="<?= $group_item->type; ?>">
										<input type="hidden" name="submittingagain" value="">

										<?php
											$result_template = '';
											switch ($group_item->type){
												case "aSuspiciousLinkText":
												case "aLinkTextDoesNotBeginWithRedundantWord":
													$result_template = 'link';
													break;

												case "cssTextStyleEmphasize":
													$result_template = 'text_style';
													break;

												case "cssTextHasContrast":
													$result_template = 'contrast';
													break;

												case "headersHaveText":
													$result_template = 'header_text';
													break;

												case "aMustContainText":
												case "aSuspiciousLinkText":
												case "aLinkTextDoesNotBeginWithRedundantWord":
													$result_template = 'link_text';
													break;

												case "imgHasAlt":
												case "imgNonDecorativeHasAlt":
												case "imgAltIsDifferent":
												case "imgAltIsTooLong":
													$result_template = 'image_alt';
													break;

												case "tableDataShouldHaveTh":
													$result_template = 'table_header';
													break;

												case "tableThShouldHaveScope":
													$result_template = 'table_header_scope';
													break;

												case "aSuspiciousLinkText":
													$result_template = 'suspicious_link_text';
													break;
											}

											if ( ! empty($result_template)){
												echo $this->fetch("partials/result_item/{$result_template}", ['group_item' => $group_item]);
											}
										?>
									</form>
								<? endif; # if (empty($post_path) && in_array($group_item->type, $fixable_types)): ?>
							</li>
						<? endforeach; # foreach ($type_group as $group_item):  ?>
					</ol>
				</div>
			</li>
		<? endforeach; # foreach ($error_types as $type_group):?>
	</ul>
</div>
