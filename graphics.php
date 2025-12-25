<div class="section action">
	<label class="checkbox-inline">
		<input ng-model="playlist.graphics.displayLogo" type="checkbox" ng-change="uncheckOther(playlist.graphics.displayLogo, 'displayLiveLogo')"> Display Logo (telefyna/logo.png)
	</label>
	<div ng-show="playlist.graphics.displayLogo">Logo Position
	    <select ng-model="playlist.graphics.logoPosition">
    	    <option value="TOP">Top Right</option>
    	    <option value="BOTTOM">Bottom Right</option>
    	</select>
    </div>
	<br>
	<hr class="hr hr-blurry" />
	<label class="checkbox-inline" ng-if="playlist.type == 'ONLINE'">
	    <input ng-model="playlist.graphics.displayLiveLogo" type="checkbox" ng-change="uncheckOther(playlist.graphics.displayLiveLogo, 'displayLogo')"> Display Live Logo (telefyna/watermark/live.png)
    </label>
	<div ng-show="playlist.graphics.displayLiveLogo">Logo Position
		<select ng-model="playlist.graphics.logoPosition">
			<option value="TOP">Top Right</option>
			<option value="BOTTOM">Bottom Right</option>
		</select>
	</div>
	<br>
    <hr class="hr hr-blurry" />
    <label class="checkbox-inline" ng-if="playlist.type != 'ONLINE'">
        <input ng-model="playlist.graphics.displayRepeatWatermark" type="checkbox"> Display Repeat Program Watermark(telefyna/watermark/repeat.png)
    </label>
    <hr class="hr hr-blurry" />
	<br>
	<div class="section action">Ticker News/Notifications Separated by #
		<br>
		<textarea class="form-control" ng-model="playlist.graphics.news.messages" placeholder="Messages separed by #"></textarea>Starting minutes Separated by #
		<input class="form-control" ng-model="playlist.graphics.news.starts" type="text" placeholder="When (nth minute after start) separated by #">
		<div>Replays
			<input ng-model="playlist.graphics.news.replays" type="number">
		</div>
		<div>Speed
        	<select ng-model="playlist.graphics.news.speed">
        		<option value="SLOW">Slow</option>
       			<option value="FAST">Fast</option>
       			<option value="VERY_FAST">Very Fast</option>
       		</select>
        </div>
	</div>
	<div class="section action table-responsive">Lower thirds
		<table class="table table-striped">
			<thead>
				<tr>
					<th scope="col">Delete</th>
					<th scope="col">Replays</th>
					<th scope="col">File</th>
					<th scope="col">Starts separated by #</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td></td>
					<td>
						<input ng-model="lowerThird.replays" type="number">
					</td>
					<td>
						<input ng-model="lowerThird.file" type="text">
					</td>
					<td>
						<input ng-model="lowerThird.starts" type="text">
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
</div>