<div class="card p-3 mb-3 border-0">
	<h6 class="font-weight-bold mb-3">Graphic Overlays & Logos</h6>
	<div class="row align-items-center mb-2">
		<div class="col-md-6">
			<div class="form-check form-switch">
				<label class="form-check-label font-weight-bold d-flex align-items-center gap-2" style="cursor: pointer;">
					<input class="form-check-input" ng-model="playlist.graphics.displayLogo" type="checkbox" ng-change="uncheckOther(playlist.graphics.displayLogo, 'displayLiveLogo')">
					<span>Display Channel Logo (<code>telefyna/logo.png</code>)</span>
				</label>
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
				<label class="form-check-label font-weight-bold d-flex align-items-center gap-2" style="cursor: pointer;">
					<input class="form-check-input" ng-model="playlist.graphics.displayLiveLogo" type="checkbox" ng-change="uncheckOther(playlist.graphics.displayLiveLogo, 'displayLogo')">
					<span>Display Live Stream Logo (<code>telefyna/watermark/live.png</code>)</span>
				</label>
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
		<label class="form-check-label font-weight-bold d-flex align-items-center gap-2" style="cursor: pointer;">
			<input class="form-check-input" ng-model="playlist.graphics.displayRepeatWatermark" type="checkbox">
			<span>Display Repeat Program Watermark (<code>telefyna/watermark/repeat.png</code>)</span>
		</label>
	</div>
</div>

<div class="card p-3 mb-3 border-0">
	<h6 class="font-weight-bold mb-2">Ticker News & Notifications</h6>
	<textarea class="form-control mb-3" rows="4" ng-model="ui.newsMsgText" ng-change="updateNewsMsgs()" placeholder="Enter ticker messages (one per line)..."></textarea>

		<div class="row">
		<div class="col-md-6 mb-2">
			<label class="form-label small font-weight-bold">Start Minute</label>
			<input class="form-control" ng-model="playlist.graphics.news.startMinute" type="number" step="any" placeholder="e.g. 0">
		</div>
		<div class="col-md-6 mb-2 d-flex align-items-end">
			<div class="form-check form-switch mb-2">
				<input class="form-check-input" ng-model="playlist.graphics.news.showTime" type="checkbox">
				<label class="form-check-label font-weight-bold ms-2">Show Time</label>
			</div>
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
