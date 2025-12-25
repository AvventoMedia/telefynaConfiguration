<div ng-if="!isEmpty(playlist.type)">{{getPlaylistTypeDesc(playlist.type)}}</div>
<div class="section action">* Type *
	<select class="form-control multiselect-ui" ng-model="playlist.type" required>
		<option value="ONLINE">Online Stream</option>
		<option value="LOCAL_SEQUENCED">Local sequenced folder</option>
		<option value="LOCAL_RANDOMIZED">Local random folder</option>
		<option value="LOCAL_RESUMING">Local resuming folder</option>
		<option value="LOCAL_RESUMING_SAME">Local same resuming folder</option>
		<option value="LOCAL_RESUMING_NEXT">Local next resuming folder</option>
		<option value="LOCAL_RESUMING_ONE">Local one program per resuming period folder</option>
	</select>
</div>
<div class="section action" ng-if="playlist.type == 'LOCAL_RESUMING_ONE'">Resuming Period
	<select class="form-control multiselect-ui" ng-model="playlist.repeat" required>
		<option value="DAILY">Daily</option>
		<option value="WEEKLY">Weekly</option>
		<option value="MONTHLY">Monthly</option>
		<option value="QUARTERLY">Quartely</option>
		<option value="ANNUALLY">Annually</option>
	</select>
</div>