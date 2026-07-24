<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="favicon.ico" rel="icon" type="image/png">
    
    <!-- Bootstrap 5.3.3 & Font Awesome 6.5.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Core Scripts -->
    <script src="res/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="res/angular.min.js"></script>
    <script src="res/angular-cookies.js"></script>
    <script src="res/jQuery.print.js"></script>
    
    <!-- App Styles & Logic -->
    <link href="res/telefyna.css" rel="stylesheet">
    <script src="res/telefyna.js"></script>
    
    <title>Telefyna Configuration Dashboard</title>
</head>

<body ng-app="Telefyna" ng-controller="Config">

    <!-- Left Sidebar Navigation -->
    <div class="sidebar-backdrop d-lg-none" ng-show="isSidebarOpen" ng-click="isSidebarOpen = false"></div>
    <aside class="app-sidebar" ng-class="{'sidebar-open': isSidebarOpen}">
        <div class="sidebar-brand">
            <img src="telefyna.png" alt="Telefyna Logo" height="38" width="42">
            <div>
                <h1 class="sidebar-brand-title">Telefyna</h1>
                <p class="sidebar-brand-subtitle">TV Broadcast Configurator</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a class="sidebar-link" ng-class="{'active': activeTab === 'general'}" ng-click="setTab('general')">
                <i class="sidebar-link-icon fa-solid fa-sliders"></i>
                <span class="sidebar-link-text">General Settings</span>
            </a>
            <a class="sidebar-link" ng-class="{'active': activeTab === 'playlists'}" ng-click="setTab('playlists')">
                <i class="sidebar-link-icon fa-solid fa-list-check"></i>
                <span class="sidebar-link-text">Playlists & Programs</span>
                <span class="sidebar-link-badge" ng-if="getPlaylistCount() > 0">{{getPlaylistCount()}}</span>
            </a>
            <a class="sidebar-link" ng-class="{'active': activeTab === 'scheduling'}" ng-click="setTab('scheduling')">
                <i class="sidebar-link-icon fa-solid fa-calendar-days"></i>
                <span class="sidebar-link-text">Scheduling</span>
                <span class="sidebar-link-badge" ng-if="getScheduleCount() > 0">{{getScheduleCount()}}</span>
            </a>
            <a class="sidebar-link" ng-class="{'active': activeTab === 'preview'}" ng-click="setTab('preview'); initPreviewData()">
                <i class="sidebar-link-icon fa-solid fa-border-all"></i>
                <span class="sidebar-link-text">Schedule Preview</span>
            </a>
            <a class="sidebar-link" ng-class="{'active': activeTab === 'epg'}" ng-click="setTab('epg')">
                <i class="sidebar-link-icon fa-solid fa-bolt"></i>
                <span class="sidebar-link-text">EPG / DVR Export</span>
            </a>
            <a class="sidebar-link" ng-class="{'active': activeTab === 'alerts'}" ng-click="setTab('alerts')">
                <i class="sidebar-link-icon fa-solid fa-bell"></i>
                <span class="sidebar-link-text">Email Alerts</span>
            </a>
            <a class="sidebar-link" ng-class="{'active': activeTab === 'info'}" ng-click="setTab('info')">
                <i class="sidebar-link-icon fa-solid fa-circle-info"></i>
                <span class="sidebar-link-text">System Info & Demo</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="toggleTheme()">
                    <i class="fa-solid" ng-class="theme === 'dark' ? 'fa-sun text-warning' : 'fa-moon text-primary'"></i>
                    <span class="ms-1">{{theme === 'dark' ? 'Light' : 'Dark'}} Mode</span>
                </button>
            </div>
            <small class="opacity-75">v{{config.version || '1.0'}}</small>
        </div>
    </aside>

    <!-- Top Header Bar -->
    <header class="app-header">
        <button class="btn btn-outline-secondary d-lg-none me-3" type="button" ng-click="isSidebarOpen = !isSidebarOpen">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="header-breadcrumb">
            <span class="d-none d-md-inline">Telefyna Configuration</span>
            <i class="fa-solid fa-chevron-right small opacity-75 d-none d-md-inline"></i>
            <span class="header-breadcrumb-active text-capitalize">{{activeTab}}</span>
        </div>
        <div class="header-actions">
            <small class="opacity-75 me-2 d-none d-md-inline" ng-if="config.lastModified">Modified: <b>{{config.lastModified}}</b></small>
            <button class="btn btn-sm btn-outline-secondary" onclick="jQuery('#import-config').click()" type="button" title="Import JSON">
                <i class="fa-solid fa-file-import"></i><span class="d-none d-md-inline ms-1">Import</span>
            </button>
            <input hidden id="import-config" ng-model="configFile" onchange="angular.element(this).scope().importConfig(event)" type="file">
            
            <button class="btn btn-sm btn-primary" ng-click="setTab('epg')" ng-disabled="isEmpty(config.playlists)" type="button" title="Export EPG">
                <i class="fa-solid fa-bolt"></i><span class="d-none d-md-inline ms-1">Export EPG</span>
            </button>
            
            <button class="btn btn-sm btn-success" ng-click="exportConfig()" ng-disabled="isEmpty(config.playlists)" type="button" title="Export JSON">
                <i class="fa-solid fa-download"></i><span class="d-none d-md-inline ms-1">Export JSON</span>
            </button>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="app-content">

        <!-- TAB 1: PLAYLISTS & PROGRAMS -->
        <section ng-show="activeTab === 'playlists'">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="content-section-title">Playlists & Programs</h2>
                    <p class="content-section-desc">Create, manage, and structure your TV broadcast playlists, folder paths, and graphic overlays.</p>
                </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary" ng-click="playlistSidebarExpanded = !playlistSidebarExpanded">
                        <i class="fa-solid" ng-class="playlistSidebarExpanded ? 'fa-compress' : 'fa-expand'"></i> 
                        {{playlistSidebarExpanded ? 'Collapse Sidebar' : 'Show Playlists'}}
                    </button>
                    <button class="btn btn-primary" ng-click="edit = undefined; clear()" type="button">
                        <i class="fa-solid fa-plus me-1"></i> Create Playlist
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Left: Playlist Selector / List -->
                <div class="mb-4" ng-class="playlistSidebarExpanded ? 'col-lg-4' : 'd-none'">
                    <div class="card p-3 h-100 d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-folder me-2" style="color:var(--accent-purple)"></i>Configured Playlists</h6>
                        <input class="form-control mb-3" type="text" placeholder="Search by title..." ng-model="searchConfigured">
                        
                        <div class="flex-grow-1 position-relative" style="min-height: 300px;">
                            <div class="position-absolute w-100 h-100 overflow-auto" ng-if="!isEmpty(config.playlists)">
                                <div ng-repeat="(k, p) in config.playlists track by k"
                                     ng-if="isNotScheduled(p) && matchesSearch(p, searchConfigured)"
                                     class="playlist-list-item d-flex align-items-center justify-content-between p-3 mb-2 rounded-3"
                                     ng-class="{'playlist-list-active': edit === '' + k}"
                                     ng-click="editPlaylist(k)"
                                     style="cursor:pointer; border:1px solid var(--border-color); transition: all 0.15s ease;">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <span class="d-inline-block rounded-circle flex-shrink-0" style="width:10px;height:10px;background-color:{{p.color || '#3b82f6'}}"></span>
                                        <div class="overflow-hidden w-100 pe-2">
                                            <div class="fw-bold text-truncate">{{getPlaylistName(k)}} <span class="opacity-75 small fw-normal">#{{k+1}}</span></div>
                                            <div class="d-flex flex-wrap gap-1 mt-1 mb-1">
                                                <span class="badge bg-secondary" ng-if="!isEmpty(p.start)" style="font-size: 0.7rem; opacity:0.85"><i class="fa-regular fa-clock me-1"></i>{{p.start}}</span>
                                                <span class="badge bg-secondary" ng-if="!isEmpty(p.days)" style="font-size: 0.7rem; opacity:0.85"><i class="fa-regular fa-calendar-days me-1"></i>{{p.days.length}} Days</span>
                                                <span class="badge bg-secondary" ng-if="!isEmpty(p.dates)" style="font-size: 0.7rem; opacity:0.85"><i class="fa-solid fa-calendar me-1"></i>{{p.dates.length}} Dates</span>
                                                <span class="badge bg-secondary" ng-if="p.graphics.displayRepeatWatermark" style="font-size: 0.7rem; opacity:0.85"><i class="fa-solid fa-repeat me-1"></i>Repeat</span>
                                            </div>
                                            <small class="opacity-75 text-truncate d-block" style="max-width:140px">{{p.urlOrFolder || p.type}}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill" ng-class="p.active !== false ? 'bg-success' : 'bg-danger'">
                                            {{p.active !== false ? 'Active' : 'Inactive'}}
                                        </span>
                                        <i class="fa-solid fa-chevron-right small opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-4" ng-if="isEmpty(config.playlists)">
                            <i class="fa-solid fa-inbox fa-2x opacity-75 mb-2"></i>
                            <p class="opacity-75 small mb-0">No playlists yet.<br>Click <b>Create Playlist</b> to begin.</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Create / Edit Playlist Form -->
                <div class="mb-4" ng-class="playlistSidebarExpanded ? 'col-lg-8' : 'col-lg-12'">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="fa-solid fa-pen-to-square me-2" style="color:var(--accent-purple)"></i>
                                {{edit !== undefined ? 'Edit Playlist' : 'New Playlist'}}
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-danger d-flex align-items-center gap-2" ng-if="edit !== undefined" ng-click="deletePlaylist(edit)">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                                <button class="btn btn-outline-primary d-flex align-items-center gap-2" ng-if="edit !== undefined" ng-click="editPlaylist(undefined)">
                                    <i class="fa-solid fa-plus"></i> New
                                </button>
                            </div>
                        </div>
                        <div class="alert alert-info small py-2" ng-if="isEmpty(config.playlists)">
                            <i class="fa-solid fa-circle-info me-1"></i> This is your first and <b>Default</b> playlist. It plays when nothing is scheduled or when automation is disabled.
                        </div>
                        <div class="alert alert-info small py-2" ng-if="config.playlists.length == 1 && edit === undefined">
                            <i class="fa-solid fa-circle-info me-1"></i> This will be your <b>Fillers</b> playlist. It plays when programs finish before the slot ends or when internet breaks.
                        </div>
                        <form ng-submit="edit !== undefined ? revise() : add()">
                            <?php include 'playlist.php';?>
                            <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                                <button class="btn btn-outline-secondary px-4" type="button" ng-click="editPlaylist(undefined)" ng-if="edit !== undefined">Cancel</button>
                                <button class="btn btn-success px-4" type="submit">
                                    <i class="fa-solid fa-check me-2"></i> {{edit !== undefined ? 'Save Changes' : 'Add Playlist'}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 2: SCHEDULING -->
        <section ng-show="activeTab === 'scheduling'">
            <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="content-section-title">Broadcast Scheduling</h2>
                    <p class="content-section-desc">Assign time slots, weekly recurrence days, date schedules, and graphic overlays to playlists.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger" type="button" ng-click="deleteAllSchedules()" title="Delete All Schedules">
                        <i class="fa-solid fa-trash"></i> <span class="d-none d-sm-inline">Delete All</span>
                    </button>
                    <button class="btn btn-outline-secondary" ng-click="schedulingSidebarExpanded = !schedulingSidebarExpanded">
                        <i class="fa-solid" ng-class="schedulingSidebarExpanded ? 'fa-compress' : 'fa-expand'"></i> 
                        {{schedulingSidebarExpanded ? 'Collapse Sidebar' : 'Select Playlist'}}
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Sidebar -->
                <div class="mb-4" ng-class="schedulingSidebarExpanded ? 'col-lg-4' : 'd-none'">
                    <div class="card p-3 h-100 d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-list me-2" style="color:var(--accent-purple)"></i>Select Playlist</h6>
                        <input class="form-control mb-3" type="text" placeholder="Search by title..." ng-model="searchScheduling">
                        
                        <div class="flex-grow-1 position-relative" style="min-height: 300px;">
                            <div class="position-absolute w-100 h-100 overflow-auto border rounded-3 p-2 bg-card">
                                <div ng-repeat="(k, p) in config.playlists track by k"
                                     ng-if="matchesSearch(p, searchScheduling)"
                                     class="playlist-list-item d-flex align-items-center justify-content-between p-2 mb-1 rounded"
                                     ng-class="{'playlist-list-active': schedule === '' + k}"
                                     ng-click="schedulePlaylist(k)"
                                     style="cursor:pointer; transition: all 0.15s ease;">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <span class="d-inline-block rounded-circle flex-shrink-0" style="width:10px;height:10px;background-color:{{p.color || '#3b82f6'}}"></span>
                                        <div class="overflow-hidden w-100 pe-2">
                                            <div class="fw-bold text-truncate">{{getPlaylistName(k)}} <span class="opacity-75 small fw-normal">#{{k+1}}</span></div>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                <span class="badge bg-primary" ng-if="!isEmpty(getScheduleSummary(p, k))" style="font-size: 0.68rem;"><i class="fa-regular fa-clock me-1"></i>{{getScheduleSummary(p, k)}}</span>
                                                <span class="badge bg-secondary opacity-50" ng-if="isEmpty(getScheduleSummary(p, k))" style="font-size: 0.65rem;"><i class="fa-solid fa-calendar-xmark me-1"></i>Unscheduled</span>
                                                <span class="badge bg-secondary" ng-if="p.graphics.displayRepeatWatermark" style="font-size: 0.65rem; opacity:0.85"><i class="fa-solid fa-repeat"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge rounded-pill" ng-class="p.active !== false ? 'bg-success' : 'bg-danger'">
                                        {{p.active !== false ? 'Active' : 'Inactive'}}
                                    </span>
                                </div>
                                <div class="opacity-75 text-center small p-3" ng-if="isEmpty(config.playlists)">No playlists available to schedule.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="mb-4" ng-class="schedulingSidebarExpanded ? 'col-lg-8' : 'col-lg-12'">
                    <div class="card p-4">
                        <form ng-submit="scheduling()">
                            
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h5 class="fw-bold m-0 text-truncate">
                                    <i class="fa-solid fa-calendar-check me-2" style="color:var(--accent-purple)"></i>
                                    Schedule: {{!isEmpty(schedule) ? getPlaylistName(schedule) : 'Select a Playlist...'}} 
                                    <span ng-if="!isEmpty(schedule)" class="badge bg-secondary ms-2" style="font-size: 0.5em; vertical-align: middle;">#{{(schedule*1)+1}}</span>
                                    <span ng-if="!isEmpty(schedule) && !isEmpty(getScheduleSummary(config.playlists[schedule], schedule))" class="badge bg-primary ms-1" style="font-size: 0.5em; vertical-align: middle;">
                                        <i class="fa-regular fa-clock me-1"></i>{{getScheduleSummary(config.playlists[schedule], schedule)}}
                                    </span>
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" ng-model="playlist.active" type="checkbox" id="chkSchedActive">
                                    <label class="form-check-label font-weight-bold" for="chkSchedActive">Active Schedule</label>
                                </div>
                            </div>

                    <div class="card p-3 mb-4 border-0 bg-card-subtle">
                        <label class="form-label font-weight-bold mb-2">Weekly Recurrence Days</label>
                        <div class="day-chip-bar mb-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="selectQuickDays('ALL')">All Days</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="selectQuickDays('WEEKDAYS')">Weekdays</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="selectQuickDays('WEEKENDS')">Weekends</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="selectQuickDays('CLEAR')">Clear</button>
                        </div>
                        <div class="day-chips">
                            <span ng-repeat="dayObj in dayOptions" 
                                  class="day-chip" 
                                  ng-class="{'day-chip-selected': isDaySelected(dayObj.val)}" 
                                  ng-click="toggleDayChip(dayObj.val)">
                                {{dayObj.label}}
                            </span>
                        </div>
                    </div>

                    <div class="card p-3 mb-4 border-0 bg-card-subtle">
                        <label class="form-label font-weight-bold mb-2">Specific Date Schedule(s)</label>
                        <div class="row align-items-center mb-2">
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input class="form-control" type="date" ng-model="customDateInput">
                                    <button class="btn btn-outline-primary" type="button" ng-click="addDateChip()">+ Add Date</button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="addQuickDate(0)">+ Today</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="addQuickDate(1)">+ Tomorrow</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" ng-click="addQuickDate(7)">+ Next Week</button>
                                </div>
                            </div>
                        </div>
                        <div class="chip-container mb-2" ng-if="playlist.dates.length > 0">
                            <span class="chip-item me-1 mb-1" ng-repeat="(k, d) in playlist.dates track by k">
                                📅 {{d}} <span class="chip-remove ms-1" ng-click="removeDateChip(k)">&times;</span>
                            </span>
                        </div>
                        <small class="opacity-75">If no specific Days or Dates are selected but start time is defined, playlist repeats daily.</small>
                    </div>

                    <div class="card p-3 mb-4 border-0 bg-card-subtle">
                        <label class="form-label fw-bold mb-2">Broadcast Start Time (HH:mm)</label>
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-2">
                                <input class="form-control form-control-lg text-center font-weight-bold" ng-model="playlist.start" required type="text" placeholder="e.g. 08:00">
                            </div>
                            <div class="col-md-8 mb-2">
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" ng-click="playlist.start = '00:00'">Midnight (00:00)</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" ng-click="playlist.start = '06:00'">06:00</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" ng-click="playlist.start = '08:00'">08:00</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" ng-click="playlist.start = '12:00'">Noon (12:00)</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" ng-click="playlist.start = '18:00'">18:00</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" ng-click="playlist.start = '20:00'">20:00</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-3 mb-4 border-0 bg-card-subtle">
                        <h6 class="font-weight-bold mb-3"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Graphic Overlays Override</h6>
                        <?php include 'graphics.php';?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <button class="btn btn-outline-danger" type="button" ng-click="deleteSelectedSchedule()" ng-disabled="isEmpty(schedule) || isNotScheduled(config.playlists[schedule])">
                            <i class="fa-solid fa-trash me-1"></i> Delete Selected
                        </button>
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary" type="button" ng-click="clear()">Clear Form</button>
                            <button class="btn btn-success px-4" type="submit" ng-disabled="isEmpty(schedule) || isEmpty(playlist.start)">
                                <i class="fa-solid fa-calendar-check me-1"></i> Save Schedule
                            </button>
                        </div>
                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- TAB 3: SCHEDULE PREVIEW -->
        <section ng-show="activeTab === 'preview'">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="content-section-title">Schedule Preview Grid</h2>
                    <p class="content-section-desc">24-Hour Broadcast Weekly & Date schedule tracker.</p>
                </div>
                <button class="btn btn-success" type="button" ng-click="printSchedule()">
                    <i class="fa-solid fa-print me-1"></i> Print Schedule
                </button>
            </div>

            <div class="card p-4 preview-print" ng-init="ui = {gridZoom: 1}">
                <div class="d-flex align-items-center justify-content-between mb-3 d-print-none" ng-if="!isEmpty(previewData.weekly)">
                    <div class="d-flex align-items-center">
                        <label class="form-label mb-0 me-2 fw-bold text-secondary"><i class="fa-solid fa-magnifying-glass me-1"></i>Zoom:</label>
                        <input type="range" class="form-range" style="width: 150px;" min="0.4" max="1.5" step="0.1" ng-model="ui.gridZoom">
                        <span class="ms-2 fw-bold text-secondary">{{(ui.gridZoom * 100) | number:0}}%</span>
                    </div>
                </div>

                <!-- Print Header -->
                <div class="d-none d-print-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div style="flex: 1;">
                        <img src="telefyna.png" alt="Telefyna Logo" height="60">
                    </div>
                    <div style="flex: 2;" class="text-center">
                        <h3 class="fw-bold m-0" style="color: #000 !important;">{{config.name || 'Telefyna'}}'s schedule</h3>
                    </div>
                    <div style="flex: 1;" class="text-end">
                        <p class="m-0 fw-bold" style="color: #000 !important;">Version: {{config.version || '1.0'}}</p>
                    </div>
                </div>

                <div class="alert alert-warning small mb-3 d-print-none" ng-if="config.automationDisabled == true">
                    Automation is disabled. Only <b>"{{config.playlists[0].name}}"</b> will play continuously.
                </div>

                <div ng-if="!isEmpty(previewData.weekly)" class="table-responsive mb-4" ng-style="{'zoom': ui.gridZoom}">
                    <h5 class="fw-bold mb-3 text-center d-none d-print-block" style="color: #000;">Weekly</h5>
                    <h5 class="fw-bold mb-3 d-print-none"><i class="fa-solid fa-calendar-week me-2" style="color:var(--accent-purple)"></i>Weekly Schedule</h5>
                    <table class="table table-bordered table-hover align-middle text-center shadow-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Time</th>
                                <th>Sunday</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                                <th>Saturday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="(key, program) in previewData.weekly">
                                <td class="fw-bold">{{program.start}}</td>
                                <td style="background-color:{{program.slots[1].color}} !important; color:#000000;">{{program.slots[1].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[1].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                                <td style="background-color:{{program.slots[2].color}} !important; color:#000000;">{{program.slots[2].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[2].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                                <td style="background-color:{{program.slots[3].color}} !important; color:#000000;">{{program.slots[3].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[3].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                                <td style="background-color:{{program.slots[4].color}} !important; color:#000000;">{{program.slots[4].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[4].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                                <td style="background-color:{{program.slots[5].color}} !important; color:#000000;">{{program.slots[5].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[5].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                                <td style="background-color:{{program.slots[6].color}} !important; color:#000000;">{{program.slots[6].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[6].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                                <td style="background-color:{{program.slots[7].color}} !important; color:#000000;">{{program.slots[7].name}} <span class="badge bg-danger ms-1" ng-if="program.slots[7].hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div ng-if="!isEmpty(previewData.dated)" class="table-responsive" ng-style="{'zoom': ui.gridZoom}">
                    <h5 class="fw-bold mb-3 d-print-none"><i class="fa-solid fa-calendar-day me-2" style="color:var(--accent-purple)"></i>Specific Date Schedules</h5>
                    <h5 class="fw-bold mb-3 text-center d-none d-print-block" style="color: #000;">Specific Date Schedules</h5>
                    <table class="table table-bordered table-hover align-middle shadow-sm">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:120px">Time</th>
                                <th>Scheduled Playlist</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="program in previewData.dated">
                                <td>{{program.at}}</td>
                                <td style="background-color:{{program.color}} !important; color:#000000;">{{program.name}} <span class="badge bg-danger ms-1" ng-if="program.hasRepeat" style="font-size: 0.6rem; vertical-align: middle;" title="Repeat Program"><i class="fa-solid fa-repeat"></i></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- TAB 4: EPG / DVR EXPORT -->
        <section ng-show="activeTab === 'epg'">
            <div class="mb-3">
                <h2 class="content-section-title">EPG / DVR Schedule Exporter</h2>
                <p class="content-section-desc">Generate daily broadcast schedule files (.tsv / .csv) for mySDAtv and EPG guides.</p>
            </div>

            <div class="card p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">File Format</label>
                        <select class="form-select form-select-lg" ng-model="epgFormat">
                            <option value="TSV">TSV (Tab Separated - Default for mySDAtv)</option>
                            <option value="CSV">CSV (Comma Separated)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Schedule Days Range</label>
                        <select class="form-select form-select-lg" ng-model="epgDaysRange">
                            <option value="14">14 Days (mySDAtv 2-Week DVR Default)</option>
                            <option value="30">30 Days (1 Month Full Schedule)</option>
                            <option value="7">7 Days (1 Week)</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-info my-3">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info me-2"></i>Exported Column Format</h6>
                    <code>Date (DD-MM-YYYY) | Start Time (HH:mm:ss) | Duration (HH:mm:ss) | Title | Playlist Description</code>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-success btn-lg px-4" ng-click="downloadEPGFile()" ng-disabled="isEmpty(config.playlists)">
                        <i class="fa-solid fa-download me-2"></i> Download EPG Schedule
                    </button>
                </div>
            </div>
        </section>

        <!-- TAB 5: EMAIL ALERTS -->
        <section ng-show="activeTab === 'alerts'">
            <div class="mb-3">
                <h2 class="content-section-title">Email Alert Notifications</h2>
                <p class="content-section-desc">Configure SMTP email notifications for broadcast errors and status updates.</p>
            </div>

            <div class="card p-4">
                <?php include 'alert.php';?>
            </div>
        </section>

        <!-- TAB 6: GENERAL SETTINGS -->
        <section ng-show="activeTab === 'general'">
            <div class="mb-3">
                <h2 class="content-section-title">General System Settings</h2>
                <p class="content-section-desc">Global configuration for player wait timeouts, network pinging, and notification flags.</p>
            </div>

            <div class="card p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Configuration Name</label>
                        <input class="form-control" ng-model="config.name" ng-change="modifying()" type="text">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Config Version</label>
                        <input class="form-control" ng-model="config.version" ng-change="modifying()" type="text">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Player Wait / Pinging Interval (Seconds)</label>
                    <input class="form-control" ng-model="config.wait" ng-change="modifying()" type="number">
                    <small class="opacity-75">Seconds to check player status and wait for internet connectivity.</small>
                </div>

                <div class="card p-3 border-0 bg-card-subtle mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" id="chkDisableAuto" ng-model="config.automationDisabled" ng-change="modifying()" type="checkbox">
                        <label class="form-check-label font-weight-bold" for="chkDisableAuto">Disable Automation (Plays default playlist continuously)</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" id="chkDisableNotif" ng-model="config.notificationsDisabled" ng-change="modifying()" type="checkbox">
                        <label class="form-check-label font-weight-bold" for="chkDisableNotif">Disable OS Notifications</label>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top text-end">
                    <button class="btn btn-danger" type="button" ng-click="clearConfig()">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Factory Reset / Clear Configuration
                    </button>
                </div>
            </div>
        </section>

        <!-- TAB 7: SYSTEM INFO & DEMO -->
        <section ng-show="activeTab === 'info'">
            <div class="mb-3">
                <h2 class="content-section-title">System Info & Infrastructure</h2>
                <p class="content-section-desc">Telefyna architecture guide, folder layout specs, and demonstration videos.</p>
            </div>

            <div class="card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fa-brands fa-youtube me-2 text-danger"></i>Video Demonstration</h5>
                <a class="btn btn-outline-primary btn-lg" href="https://www.youtube.com/watch?v=Oy5aN6MTcXM" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square me-2"></i> Watch Telefyna Configuration Demo on YouTube
                </a>
            </div>

            <div class="card p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-sitemap me-2" style="color:var(--accent-purple)"></i>Telefyna Infrastructure Diagram</h5>
                <div class="text-center">
                    <img src="telefynaDesign.png" alt="Telefyna Infrastructure" class="rounded border" style="max-width:600px; width:100%;">
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="app-footer text-center py-4 mt-auto border-top">
            <div class="container">
                <p class="mb-1">
                    &copy; <?php echo date('Y'); ?> AvventoMedia. All rights reserved. |
                    <a href="https://avventomedia.org/terms" target="_blank" rel="noopener noreferrer">Terms of Use</a> |
                    <a href="https://avventomedia.org/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                </p>
                <p class="mb-1 fst-italic opacity-75">Flying the Gospel to the Whole World</p>
                <p class="mb-0 small opacity-50">Built with purpose for ministry.</p>
            </div>
        </footer>

    </main>

</body>
</html>
