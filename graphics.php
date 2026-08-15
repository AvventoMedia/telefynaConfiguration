<div class="card p-3 mb-3 border-0">
	<h6 class="font-weight-bold mb-3">Graphic Overlays & Logos</h6>
	<div class="row align-items-center mb-2">
		<div class="col-md-6">
			<div class="form-check form-switch">
				<input class="form-check-input" id="chkDisplayLogo" ng-model="playlist.graphics.displayLogo" type="checkbox" ng-change="uncheckOther(playlist.graphics.displayLogo, 'displayLiveLogo')">
				<label class="form-check-label font-weight-bold" for="chkDisplayLogo">Display Channel Logo (<code>telefyna/logo.png</code>)</label>
			</div>
		</div>
		<div class="col-md-6" ng-show="playlist.graphics.displayLogo">
			<label class="form-label small font-weight-bold mb-1">Logo Position</label>
			<select class="form-select" ng-model="playlist.graphics.logoPosition">
				<option value="TOP">Top Right</option>
				<option value="BOTTOM">Bottom Right</option>
			</select>
		</div>
	</div>

	<div class="row align-items-center mb-2" ng-if="playlist.type == 'ONLINE'">
		<div class="col-md-6">
			<div class="form-check form-switch">
				<input class="form-check-input" id="chkLiveLogo" ng-model="playlist.graphics.displayLiveLogo" type="checkbox" ng-change="uncheckOther(playlist.graphics.displayLiveLogo, 'displayLogo')">
				<label class="form-check-label font-weight-bold" for="chkLiveLogo">Display Live Stream Logo (<code>telefyna/watermark/live.png</code>)</label>
			</div>
		</div>
		<div class="col-md-6" ng-show="playlist.graphics.displayLiveLogo">
			<label class="form-label small font-weight-bold mb-1">Logo Position</label>
			<select class="form-select" ng-model="playlist.graphics.logoPosition">
				<option value="TOP">Top Right</option>
				<option value="BOTTOM">Bottom Right</option>
			</select>
		</div>
	</div>

	<div class="form-check form-switch mt-2" ng-if="playlist.type != 'ONLINE'">
		<input class="form-check-input" id="chkRepeatWatermark" ng-model="playlist.graphics.displayRepeatWatermark" type="checkbox">
		<label class="form-check-label font-weight-bold" for="chkRepeatWatermark">Display Repeat Program Watermark (<code>telefyna/watermark/repeat.png</code>)</label>
	</div>
</div>

<div class="card p-3 mb-3 border-0">
	<h6 class="font-weight-bold mb-2">Ticker News & Notifications</h6>
	<textarea class="form-control mb-3" rows="4" ng-model="ui.newsMsgText" ng-change="updateNewsMsgs()" placeholder="Enter ticker messages (one per line)..."></textarea>

	<div class="row">
		<div class="col-md-12 mb-2">
			<label class="form-label small font-weight-bold">Start Minute</label>
			<input class="form-control" ng-model="playlist.graphics.news.startMinute" type="number" step="any" placeholder="e.g. 0">
		</div>
	</div>
</div>

<div class="card p-3 border-0 table-responsive">
	<h6 class="font-weight-bold mb-2">Lower Third Overlays</h6>
	<table class="table table-striped">
		<thead>
			<tr>
				<th scope="col">Delete</th>
				<th scope="col">Replays</th>
				<th scope="col">File Name</th>
				<th scope="col">Starts (Minutes) <span class="custom-tooltip-wrapper ms-1" data-tooltip="Separate multiple start times with commas (e.g. 0, 10, 15)"><i class="fa-solid fa-circle-info" style="opacity: 0.85; cursor: help;"></i></span></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
				<td>
					<input class="form-control" ng-model="lowerThird.replays" type="number" placeholder="1">
				</td>
				<td>
					<input class="form-control" ng-model="lowerThird.file" type="text" placeholder="lowerThird.png">
				</td>
				<td>
					<input class="form-control" ng-model="lowerThird.starts" type="text" placeholder="0, 10">
				</td>
			</tr>
			<tr ng-repeat="(k, l) in playlist.graphics.lowerThirds">
				<td>
					<input class="lower-third-action" value="{{k}}" type="checkbox">
				</td>
				<td>{{l.replays}}</td>
				<td>{{l.file}}</td>
				<td>{{l.starts}}</td>
			</tr>
		</tbody>
	</table>
</div>