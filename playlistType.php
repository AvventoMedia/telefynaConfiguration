<div class="mb-3">
	<label class="form-label fw-bold mb-2">Select Playlist Type</label>
	<div class="playlist-type-grid">
		<div ng-repeat="t in playlistTypes" 
		     class="type-card" 
		     ng-class="{'type-card-active': playlist.type === t.key}" 
		     ng-click="selectPlaylistType(t.key)">
			<div class="radio-indicator">
				<div class="radio-dot" ng-if="playlist.type === t.key"></div>
			</div>
			<div class="type-card-icon"><i class="{{t.iconClass}}"></i></div>
			<div class="type-card-content">
				<div class="type-card-title">{{t.title}}</div>
				<div class="type-card-desc">{{t.desc}}</div>
			</div>
		</div>
	</div>
</div>

<div class="card p-3 mb-3 border-0" ng-if="playlist.type == 'LOCAL_RESUMING_ONE'">
	<label class="form-label fw-bold mb-2">Resuming Frequency Period</label>
	<div class="d-flex flex-wrap gap-2">
		<button type="button" 
		        ng-repeat="p in ['DAILY', 'WEEKLY', 'MONTHLY', 'QUARTERLY', 'ANNUALLY']" 
		        class="btn mr-2 mb-2" 
		        ng-class="playlist.repeat === p ? 'btn-primary font-weight-bold' : 'btn-outline-secondary'" 
		        ng-click="playlist.repeat = p">
			{{p}}
		</button>
	</div>
</div>