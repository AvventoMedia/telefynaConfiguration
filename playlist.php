<div class="alert alert-danger" ng-show="!isEmpty(error)" role="alert">{{error}}</div>

<div class="card p-3 mb-3 border-0" ng-if="(edit != 0 && edit != 1 && !isEmpty(config.playlists) && config.playlists.length != 1) || !isEmpty(edit)">
	<div class="form-check form-switch mb-2" ng-if="edit != 0 && edit != 1 && !isEmpty(config.playlists) && config.playlists.length != 1">
		<input class="form-check-input" ng-model="playlist.active" type="checkbox" id="chkActive">
		<label class="form-check-label font-weight-bold" for="chkActive">Active Playlist / Schedule</label>
	</div>
	<div class="form-check form-switch" ng-if="!isEmpty(edit)">
		<input class="form-check-input" ng-model="overrideSchedules" type="checkbox" id="chkOverride">
		<label class="form-check-label font-weight-bold" for="chkOverride">Force all Schedules to inherit these Graphics</label>
	</div>
</div>

<div class="row">
	<div class="col-md-6 mb-3">
		<label class="form-label fw-bold mb-2">Playlist Name</label>
		<input class="form-control" ng-model="playlist.name" placeholder="e.g. Morning Broadcast" required type="text">
	</div>
	<div class="col-md-6 mb-3">
		<label class="form-label font-weight-bold">Description</label>
		<input class="form-control" ng-model="playlist.description" placeholder="Brief description..." type="text">
	</div>
</div>

<?php include 'playlistType.php';?>

<div class="row mt-3">
	<div class="col-12 mb-3">
		<label class="form-label font-weight-bold">
			<span ng-if="playlist.type == 'ONLINE'" class="fw-bold">Stream URL</span>
			<span ng-if="playlist.type != 'ONLINE'" class="fw-bold">Local Folder Name <span class="custom-tooltip-wrapper ms-1" data-tooltip="Separate multiple folders or paths with commas (e.g. Folder1,Folder2/Subfolder)"><i class="fa-solid fa-circle-info" style="opacity: 0.85; cursor: help;"></i></span></span>
		</label>
		<input class="form-control" ng-model="playlist.urlOrFolder" placeholder="Folder or URL..." required type="text">
	</div>
	<div class="col-12 mb-3">
		<label class="form-label font-weight-bold">Empty Replacer Playlist Index</label>
		<input class="form-control" ng-model="playlist.emptyReplacer" placeholder="Optional playlist index" type="number">
	</div>
</div>

<div class="row" ng-if="playlist.type.indexOf('LOCAL_RESUMING') == 0">
	<div class="col-md-6 mb-3">
		<label class="form-label font-weight-bold">SeekTo Starting Program Index (0-based)</label>
		<input class="form-control" ng-model="playlist.seekTo.program" type="number">
	</div>
	<div class="col-md-6 mb-3">
		<label class="form-label font-weight-bold">SeekTo Position (Milliseconds)</label>
		<input class="form-control" ng-model="playlist.seekTo.position" type="number">
	</div>
</div>

<div class="card p-3 mb-3 border-0">
	<label class="form-label font-weight-bold mb-2">Schedule Color Indicator</label>
	<div class="color-swatch-grid">
		<span ng-repeat="c in availableColors" 
			  class="color-swatch-item" 
			  ng-style="{'background-color': c}" 
			  ng-class="{'color-swatch-active': playlist.color === c}" 
			  ng-click="selectColorSwatch(c)">
			<span ng-if="playlist.color === c" class="text-white font-weight-bold">✓</span>
		</span>
	</div>
</div>

<div class="mb-3" ng-if="playlist.type != 'ONLINE'">
	<label class="form-label font-weight-bold mb-2">Storage Source</label>
	<div class="playlist-type-grid" style="grid-template-columns: 1fr;">
		<div class="type-card" 
		     ng-class="{'type-card-active': playlist.usingExternalStorage}" 
		     ng-click="playlist.usingExternalStorage = !playlist.usingExternalStorage">
			<div class="radio-indicator">
				<div class="radio-dot" ng-if="playlist.usingExternalStorage"></div>
			</div>
			<div class="type-card-icon"><i class="fa-solid fa-hard-drive" ng-class="playlist.usingExternalStorage ? 'text-primary' : 'text-secondary'"></i></div>
			<div class="type-card-content">
				<div class="type-card-title">Use External Storage (SDCard / USB Drive)</div>
				<div class="type-card-desc">Read folders from <code>/telefyna</code> on attached SDCard or USB drive instead of internal storage.</div>
			</div>
		</div>
	</div>
</div>

<div class="mb-3" ng-if="playlist.type == 'LOCAL_SEQUENCED' || playlist.type == 'LOCAL_RANDOMIZED'">
	<label class="form-label font-weight-bold mb-2">Bumper Settings</label>
	<div class="row">
		<div class="col-md-6 mb-3">
			<div class="type-card h-100" 
			     ng-class="{'type-card-active': playlist.playingGeneralBumpers}" 
			     ng-click="playlist.playingGeneralBumpers = !playlist.playingGeneralBumpers">
				<div class="radio-indicator">
					<div class="radio-dot" ng-if="playlist.playingGeneralBumpers"></div>
				</div>
				<div class="type-card-icon"><i class="fa-solid fa-film" ng-class="playlist.playingGeneralBumpers ? 'text-primary' : 'text-secondary'"></i></div>
				<div class="type-card-content">
					<div class="type-card-title">Play General Bumpers</div>
					<div class="type-card-desc">Interleave standard bumpers between videos.</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 mb-3">
			<div class="card p-3 h-100 border-0 bg-card-subtle" style="border-radius: 12px; border: 2px solid transparent;">
				<label class="form-label font-weight-bold mb-2">Special Bumper Folder <span class="custom-tooltip-wrapper ms-1" data-tooltip="Override the default bumper folder for this specific playlist."><i class="fa-solid fa-circle-info" style="opacity: 0.85; cursor: help;"></i></span></label>
				<input class="form-control" ng-model="playlist.specialBumperFolder" placeholder="Folder Name..." type="text">
			</div>
		</div>
	</div>
	</div>
</div>
								
<div class="mt-4 pt-3 border-top">
	<h6 class="fw-bold mb-3"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Default Graphic Overlays</h6>
	<?php include 'graphics.php';?>
</div>