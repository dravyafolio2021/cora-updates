/**
 * Cora Field Ops & Real-Time Geolocation Tracker
 * Monochromatic route inspection, stop/rest detection, and time-scrub playback engine.
 * 
 * Complies with Cora Platform Global Rules:
 * - Monochromatic palette (zinc-50 to zinc-950, pure white, pure black).
 * - Zero browser-native alerts (monochromatic toasts only).
 * - Scoped DOM selectors and namespacing.
 */

(function(window, document, $) {
    'use strict';

    window.CoraFieldOps = {
        map: null,
        markersLayer: null,
        routeLayer: null,
        animMarker: null,
        currentRouteData: null,
        replayTimer: null,
        replayIndex: 0,
        replaySpeed: 1,
        isPlaying: false,
        watchId: null,
        offlineQueueKey: 'cora_gps_telemetry_queue',
        lastPosition: null,
        minDistanceMeters: 15,
        syncIntervalMs: 25000,
        timerInterval: null,

        /**
         * Initialize Field Ops Tracker
         */
        init: function() {
            var self = this;
            this.ensureLeafletLoaded(function() {
                self.initMap();
                self.bindUIEvents();
                self.loadLiveFieldOps();
            });

            // Start offline queue flush watcher
            setInterval(function() {
                self.flushOfflineQueue();
            }, 60000);
            
            // Check if active punch-in exists to resume watcher
            if (window.coraCurrentPunchIn && window.coraCurrentPunchIn.active) {
                this.startTelemetry(window.coraCurrentPunchIn.id);
            }
        },

        /**
         * Ensure Leaflet CSS & JS are dynamically loaded
         */
        ensureLeafletLoaded: function(callback) {
            if (window.L && window.L.map) {
                if (typeof callback === 'function') callback();
                return;
            }

            // Load Leaflet CSS
            if (!document.getElementById('leaflet-css')) {
                var cssLink = document.createElement('link');
                cssLink.id = 'leaflet-css';
                cssLink.rel = 'stylesheet';
                cssLink.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(cssLink);
            }

            // Load Leaflet JS
            if (!document.getElementById('leaflet-js')) {
                var script = document.createElement('script');
                script.id = 'leaflet-js';
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = function() {
                    if (typeof callback === 'function') callback();
                };
                document.head.appendChild(script);
            } else {
                var checkInterval = setInterval(function() {
                    if (window.L && window.L.map) {
                        clearInterval(checkInterval);
                        if (typeof callback === 'function') callback();
                    }
                }, 100);
            }
        },

        /**
         * Initialize Monochromatic Leaflet Map
         */
        initMap: function() {
            var mapContainer = document.getElementById('cora-field-ops-map');
            if (!mapContainer || this.map) return;

            // Default center: India/Bengaluru or fallback
            this.map = L.map('cora-field-ops-map', {
                center: [12.9716, 77.5946],
                zoom: 13,
                zoomControl: false,
                attributionControl: false
            });

            // 100% Free, Zero-API-Key OpenStreetMap Tiles with Monochromatic Filter
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                className: 'cora-monochrome-tiles',
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(this.map);

            // Clean custom zoom control at bottom-right
            L.control.zoom({ position: 'bottomright' }).addTo(this.map);

            this.markersLayer = L.layerGroup().addTo(this.map);
            this.routeLayer = L.layerGroup().addTo(this.map);

            // Fix map display when tab becomes visible
            var self = this;
            setTimeout(function() {
                if (self.map) self.map.invalidateSize();
            }, 300);
        },

        /**
         * Bind UI Controls
         */
        bindUIEvents: function() {
            var self = this;

            // Shift Selector Change
            $('#field-ops-user-select, #field-ops-date-select').on('change', function() {
                self.fetchEmployeeRoute();
            });

            // Replay Play / Pause
            $('#field-ops-play-btn').on('click', function() {
                self.toggleReplay();
            });

            // Replay Reset
            $('#field-ops-reset-btn').on('click', function() {
                self.resetReplay();
            });

            // Replay Speed selector
            $('#field-ops-speed-select').on('change', function() {
                self.replaySpeed = parseFloat($(this).val()) || 1;
                if (self.isPlaying) {
                    self.pauseReplay();
                    self.playReplay();
                }
            });

            // Replay Range Scrubber
            $('#field-ops-scrubber').on('input', function() {
                var index = parseInt($(this).val(), 10);
                self.seekTo(index);
            });

            // Refresh Live Operations
            $('#field-ops-refresh-live-btn').on('click', function() {
                self.loadLiveFieldOps();
            });

            // Simulation Demo Button
            $('#field-ops-demo-sim-btn').on('click', function() {
                self.simulateDemoShift();
            });
        },

        /**
         * Real-time GPS Telemetry Watcher
         */
        startTelemetry: function(punchId) {
            var self = this;
            if (!navigator.geolocation) {
                console.warn('[Cora FieldOps] Geolocation is not supported by this device.');
                return;
            }

            this.stopTelemetry();

            var watchOptions = {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 5000
            };

            this.watchId = navigator.geolocation.watchPosition(
                function(pos) {
                    self.handlePositionUpdate(pos, punchId);
                },
                function(err) {
                    console.warn('[Cora FieldOps] GPS watch error:', err.message);
                },
                watchOptions
            );

            // Heartbeat batch sync timer
            this.timerInterval = setInterval(function() {
                self.flushOfflineQueue();
            }, this.syncIntervalMs);

            console.log('[Cora FieldOps] Telemetry watcher started for punch #' + punchId);
        },

        stopTelemetry: function() {
            if (this.watchId !== null) {
                navigator.geolocation.clearWatch(this.watchId);
                this.watchId = null;
            }
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
            this.flushOfflineQueue();
            console.log('[Cora FieldOps] Telemetry watcher stopped.');
        },

        handlePositionUpdate: function(pos, punchId) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            var accuracy = pos.coords.accuracy || 0;
            var speed = pos.coords.speed !== null && !isNaN(pos.coords.speed) ? pos.coords.speed * 3.6 : 0; // km/h
            var heading = pos.coords.heading || 0;
            var altitude = pos.coords.altitude || 0;
            var recordedAt = new Date(pos.timestamp || Date.now()).toISOString().replace('T', ' ').substring(0, 19);

            // Filter micro-jitter if stationary
            if (this.lastPosition) {
                var dist = this.calculateDistance(this.lastPosition.lat, this.lastPosition.lng, lat, lng);
                if (dist < this.minDistanceMeters && speed < 1.0) {
                    return; // Ignore jitter under 15m when not moving
                }
            }

            this.lastPosition = { lat: lat, lng: lng };

            // Determine battery level if battery API is available
            var batteryLevel = null;
            if (navigator.getBattery) {
                navigator.getBattery().then(function(battery) {
                    batteryLevel = Math.round(battery.level * 100);
                });
            }

            var point = {
                punch_id: punchId || 0,
                lat: lat,
                lng: lng,
                accuracy: accuracy,
                speed: speed,
                heading: heading,
                altitude: altitude,
                battery_level: batteryLevel,
                recorded_at: recordedAt
            };

            this.queueTelemetry(point);
        },

        queueTelemetry: function(point) {
            try {
                var queue = JSON.parse(localStorage.getItem(this.offlineQueueKey) || '[]');
                queue.push(point);
                localStorage.setItem(this.offlineQueueKey, JSON.stringify(queue));
                
                // If queue reaches 5 points or online, sync
                if (queue.length >= 3 && navigator.onLine) {
                    this.flushOfflineQueue();
                }
            } catch(e) {
                console.error('[Cora FieldOps] LocalStorage error:', e);
            }
        },

        flushOfflineQueue: function() {
            var self = this;
            var raw = localStorage.getItem(this.offlineQueueKey);
            if (!raw) return;

            var queue = JSON.parse(raw || '[]');
            if (!queue || queue.length === 0) return;

            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) || (window.coraWorkspaceData && window.coraWorkspaceData.ajaxUrl) || '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) || (window.coraWorkspaceData && window.coraWorkspaceData.ajaxNonce) || '';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_sync_gps_telemetry',
                    nonce: nonce,
                    points: JSON.stringify(queue)
                },
                success: function(res) {
                    if (res && res.success) {
                        localStorage.removeItem(self.offlineQueueKey);
                    }
                },
                error: function(err) {
                    console.warn('[Cora FieldOps] Telemetry sync network retry scheduled.');
                }
            });
        },

        /**
         * Fetch and Render Full Route for Selected Employee & Date
         */
        fetchEmployeeRoute: function(userId, dateStr) {
            var self = this;
            var selectedUser = userId || $('#field-ops-user-select').val();
            var selectedDate = dateStr || $('#field-ops-date-select').val();

            if (!selectedUser) {
                $('#field-ops-empty-state').removeClass('hidden');
                $('#field-ops-route-view').addClass('hidden');
                return;
            }

            $('#field-ops-loader').removeClass('hidden');
            $('#field-ops-empty-state').addClass('hidden');
            $('#field-ops-route-view').addClass('hidden');

            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) || '';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_get_employee_route',
                    nonce: nonce,
                    user_id: selectedUser,
                    date: selectedDate
                },
                success: function(res) {
                    $('#field-ops-loader').addClass('hidden');
                    if (res && res.success && res.data.points && res.data.points.length > 0) {
                        self.currentRouteData = res.data;
                        $('#field-ops-route-view').removeClass('hidden');
                        self.renderRouteMap(res.data);
                        self.renderSummaryMetrics(res.data.summary);
                        self.renderTimeline(res.data.legs, res.data.stops);
                        self.setupReplayControls(res.data.points);
                    } else {
                        $('#field-ops-empty-state').removeClass('hidden');
                        var msg = (res && res.data && res.data.message) ? res.data.message : 'No geolocation trackpoints recorded for this shift.';
                        $('#field-ops-empty-msg').text(msg);
                    }
                },
                error: function() {
                    $('#field-ops-loader').addClass('hidden');
                    $('#field-ops-empty-state').removeClass('hidden');
                    $('#field-ops-empty-msg').text('Failed to retrieve shift telemetry. Please try again.');
                }
            });
        },

        /**
         * Render Monochromatic Map with Route Polyline & Stop Badges
         */
        renderRouteMap: function(data) {
            var self = this;
            if (!this.map) this.initMap();
            if (!this.map) return;

            this.markersLayer.clearLayers();
            this.routeLayer.clearLayers();

            var points = data.points || [];
            if (points.length === 0) return;

            var latLngs = points.map(function(p) {
                return [parseFloat(p.lat), parseFloat(p.lng)];
            });

            // Draw Route Polyline (Monochromatic Slate-900 / Zinc-900 with subtle glow)
            var polyline = L.polyline(latLngs, {
                color: '#18181b',
                weight: 4,
                opacity: 0.9,
                lineJoin: 'round',
                dashArray: null
            }).addTo(this.routeLayer);

            // Add directional pulse/glow line underneath
            L.polyline(latLngs, {
                color: '#71717a',
                weight: 8,
                opacity: 0.15,
                lineJoin: 'round'
            }).addTo(this.routeLayer);

            // Fit bounds with padding & invalidate size
            setTimeout(function() {
                if (self.map) {
                    self.map.invalidateSize();
                    self.map.fitBounds(polyline.getBounds(), {
                        padding: [40, 40],
                        maxZoom: 16
                    });
                }
            }, 50);

            // 1. Start Marker (Punch In)
            var startPt = points[0];
            var startIcon = L.divIcon({
                className: 'cora-custom-map-pin',
                html: '<div class="w-7 h-7 rounded-full bg-zinc-950 border-2 border-white shadow-lg flex items-center justify-center text-white text-[10px] font-bold"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            var startMarker = L.marker([startPt.lat, startPt.lng], { icon: startIcon }).addTo(this.markersLayer);
            startMarker.bindPopup('<div class="p-2 text-xs font-sans space-y-1"><div class="font-bold text-zinc-950 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Shift Started (Punch In)</div><div class="text-zinc-500 text-[11px]">' + (startPt.recorded_at || '') + '</div></div>');

            // 2. End / Latest Marker (Punch Out or Live Pin)
            var endPt = points[points.length - 1];
            var endIcon = L.divIcon({
                className: 'cora-custom-map-pin',
                html: '<div class="w-7 h-7 rounded-full bg-zinc-950 border-2 border-white shadow-lg flex items-center justify-center text-white text-[10px] font-bold"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            var endMarker = L.marker([endPt.lat, endPt.lng], { icon: endIcon }).addTo(this.markersLayer);
            endMarker.bindPopup('<div class="p-2 text-xs font-sans space-y-1"><div class="font-bold text-zinc-950 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-zinc-900"></span> Final Recorded Position</div><div class="text-zinc-500 text-[11px]">' + (endPt.recorded_at || '') + '</div></div>');

            // 3. Stop / Rest Markers
            var stops = data.stops || [];
            stops.forEach(function(st) {
                var badgeColor = 'bg-zinc-900 text-white';
                var stopLabel = 'Quick Stop';
                if (st.type === 'site_visit') {
                    stopLabel = 'Site Visit';
                    badgeColor = 'bg-zinc-800 text-white';
                } else if (st.type === 'rest_break') {
                    stopLabel = 'Rest Break';
                    badgeColor = 'bg-zinc-950 text-white';
                }

                var stopIcon = L.divIcon({
                    className: 'cora-stop-map-pin',
                    html: '<div class="relative group cursor-pointer">' +
                          '  <div class="w-6 h-6 rounded-full ' + badgeColor + ' border-2 border-white shadow-md flex items-center justify-center text-[10px] font-bold transition-transform group-hover:scale-125">' + st.index + '</div>' +
                          '  <div class="absolute -top-6 left-1/2 -translate-x-1/2 px-1.5 py-0.5 rounded bg-zinc-900 text-white text-[9px] font-mono whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow">' + st.duration_formatted + '</div>' +
                          '</div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                var stopMarker = L.marker([st.lat, st.lng], { icon: stopIcon }).addTo(self.markersLayer);
                var popupHtml = '<div class="p-2.5 text-xs font-sans space-y-1.5 min-w-[180px]">' +
                                '  <div class="flex items-center justify-between gap-2 border-b border-zinc-100 pb-1.5">' +
                                '    <span class="font-bold text-zinc-900">Stop #' + st.index + ' (' + stopLabel + ')</span>' +
                                '    <span class="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-700 text-[10px] font-bold font-mono">' + st.duration_formatted + '</span>' +
                                '  </div>' +
                                '  <div class="grid grid-cols-2 gap-1 text-[11px] text-zinc-600">' +
                                '    <div><span class="text-zinc-400 block text-[9px] uppercase font-bold">Arrived</span> ' + st.arrival_time.substring(11, 16) + '</div>' +
                                '    <div><span class="text-zinc-400 block text-[9px] uppercase font-bold">Departed</span> ' + st.departure_time.substring(11, 16) + '</div>' +
                                '  </div>' +
                                '  <div class="text-[10px] text-zinc-400 font-mono pt-1">' + parseFloat(st.lat).toFixed(5) + ', ' + parseFloat(st.lng).toFixed(5) + '</div>' +
                                '</div>';
                stopMarker.bindPopup(popupHtml);
            });

            // 4. Setup Replay Avatar Marker
            var replayIcon = L.divIcon({
                className: 'cora-replay-avatar-pin',
                html: '<div class="w-8 h-8 rounded-full bg-zinc-950 border-2 border-white shadow-xl flex items-center justify-center text-white ring-4 ring-zinc-950/20 animate-pulse">' +
                      '  <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>' +
                      '</div>',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
            this.animMarker = L.marker([startPt.lat, startPt.lng], { icon: replayIcon, zIndexOffset: 1000 }).addTo(this.markersLayer);
        },

        /**
         * Render Summary KPI Cards
         */
        renderSummaryMetrics: function(s) {
            if (!s) return;
            $('#field-ops-kpi-dist').text(s.total_distance_km + ' km');
            $('#field-ops-kpi-transit').text(s.transit_time_formatted || '0m');
            $('#field-ops-kpi-dwell').text(s.dwell_time_formatted || '0m');
            $('#field-ops-kpi-stops').text(s.stop_count || 0);
            $('#field-ops-kpi-speed').text(s.avg_speed_kmh + ' km/h (Max: ' + s.max_speed_kmh + ')');
        },

        /**
         * Render Step-by-Step Chronological Leg Timeline
         */
        renderTimeline: function(legs, stops) {
            var $list = $('#field-ops-timeline-list');
            $list.empty();

            if (!legs || legs.length === 0) {
                $list.html('<div class="p-4 text-center text-xs text-zinc-400">No shift legs recorded.</div>');
                return;
            }

            legs.forEach(function(leg, idx) {
                var iconHtml = '';
                var title = '';
                var badge = '';
                var subtitle = '';

                if (leg.type === 'punch_in') {
                    iconHtml = '<div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shadow-xs"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></div>';
                    title = 'Shift Started • Punch In';
                    var timeStr = leg.time || (leg.start_time ? leg.start_time.substring(11, 16) : '--:--');
                    badge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Start ' + timeStr + '</span>';
                    subtitle = '<span class="text-zinc-500">Continuous telemetry initiated</span>';
                } else if (leg.type === 'punch_out') {
                    iconHtml = '<div class="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shadow-xs"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg></div>';
                    title = 'Shift Concluded • Punch Out';
                    var timeStr = leg.time || (leg.end_time ? leg.end_time.substring(11, 16) : '--:--');
                    badge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 text-zinc-800 border border-zinc-200">End ' + timeStr + '</span>';
                    subtitle = '<span class="text-zinc-500">Telemetry session closed</span>';
                } else if (leg.type === 'stop') {
                    var stopNum = (leg.stop_data && leg.stop_data.index) ? leg.stop_data.index : (leg.index || idx);
                    var stopLabel = leg.label || (leg.stop_data && leg.stop_data.badge_label) || 'Site Visit';
                    iconHtml = '<div class="w-6 h-6 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold shadow-sm">' + stopNum + '</div>';
                    title = 'Stop #' + stopNum + ' • ' + stopLabel;
                    badge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-zinc-900 text-white">' + (leg.duration_formatted || leg.dwell_human || '0m') + '</span>';
                    var arrivalStr = leg.start_formatted || (leg.start_time ? leg.start_time.substring(11, 16) : '');
                    var departStr = leg.end_formatted || (leg.end_time ? leg.end_time.substring(11, 16) : '');
                    subtitle = '<span class="text-zinc-500">' + (arrivalStr && departStr ? arrivalStr + ' → ' + departStr : '') + '</span>';
                } else {
                    // Transit leg
                    iconHtml = '<div class="w-6 h-6 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 flex items-center justify-center text-[10px] font-bold"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg></div>';
                    title = 'Transit Leg';
                    var dist = leg.distance_km || 0;
                    var dur = leg.duration_formatted || leg.duration_human || '';
                    badge = '<span class="px-2 py-0.5 rounded text-[10px] font-medium font-mono bg-zinc-100 text-zinc-700 border border-zinc-200">' + dist + ' km' + (dur ? ' (' + dur + ')' : '') + '</span>';
                    var startT = leg.start_formatted || (leg.start_time ? leg.start_time.substring(11, 16) : '');
                    var endT = leg.end_formatted || (leg.end_time ? leg.end_time.substring(11, 16) : '');
                    var speedStr = leg.avg_speed_kmh ? ' • Avg ' + leg.avg_speed_kmh + ' km/h' : '';
                    subtitle = '<span class="text-zinc-500">' + (startT && endT ? startT + ' → ' + endT : '') + speedStr + '</span>';
                }

                var itemHtml = '<div class="flex items-start gap-3 p-3 rounded-xl border border-zinc-200/80 bg-white hover:border-zinc-300 transition-colors shadow-xs w-full">' +
                               '  <div class="shrink-0 mt-0.5">' + iconHtml + '</div>' +
                               '  <div class="flex-1 min-w-0 space-y-1">' +
                               '    <div class="flex items-center justify-between gap-2">' +
                               '      <h4 class="text-xs font-bold text-zinc-900 truncate">' + title + '</h4>' +
                               '      ' + badge +
                               '    </div>' +
                               '    <div class="flex items-center justify-between text-[11px]">' +
                               '      ' + subtitle +
                               '    </div>' +
                               '  </div>' +
                               '</div>';

                $list.append(itemHtml);
            });
        },

        /**
         * Setup Replay Range Scrubber & Timeline Player
         */
        setupReplayControls: function(points) {
            this.pauseReplay();
            this.replayIndex = 0;
            var maxIndex = Math.max(0, points.length - 1);
            $('#field-ops-scrubber').attr('max', maxIndex).val(0);
            this.updateReplayHUD(0);
        },

        toggleReplay: function() {
            if (this.isPlaying) {
                this.pauseReplay();
            } else {
                this.playReplay();
            }
        },

        playReplay: function() {
            var self = this;
            if (!this.currentRouteData || !this.currentRouteData.points || this.currentRouteData.points.length === 0) return;

            var points = this.currentRouteData.points;
            if (this.replayIndex >= points.length - 1) {
                this.replayIndex = 0;
            }

            this.isPlaying = true;
            $('#field-ops-play-btn').html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg> <span>Pause</span>');

            var baseInterval = 1000; // 1 sec per step at 1x
            var intervalMs = Math.max(50, Math.floor(baseInterval / this.replaySpeed));

            this.replayTimer = setInterval(function() {
                if (self.replayIndex < points.length - 1) {
                    self.replayIndex++;
                    self.seekTo(self.replayIndex, true);
                } else {
                    self.pauseReplay();
                }
            }, intervalMs);
        },

        pauseReplay: function() {
            this.isPlaying = false;
            if (this.replayTimer) {
                clearInterval(this.replayTimer);
                this.replayTimer = null;
            }
            $('#field-ops-play-btn').html('<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg> <span>Play Replay</span>');
        },

        resetReplay: function() {
            this.pauseReplay();
            this.seekTo(0);
        },

        seekTo: function(index, isFromPlayback) {
            if (!this.currentRouteData || !this.currentRouteData.points) return;
            var points = this.currentRouteData.points;
            if (index < 0 || index >= points.length) return;

            this.replayIndex = index;
            if (!isFromPlayback) {
                $('#field-ops-scrubber').val(index);
            } else {
                $('#field-ops-scrubber').val(index);
            }

            var pt = points[index];
            if (this.animMarker && pt) {
                this.animMarker.setLatLng([parseFloat(pt.lat), parseFloat(pt.lng)]);
                if ($('#field-ops-follow-checkbox').is(':checked')) {
                    this.map.panTo([parseFloat(pt.lat), parseFloat(pt.lng)], { animate: true, duration: 0.3 });
                }
            }

            this.updateReplayHUD(index);
        },

        updateReplayHUD: function(index) {
            if (!this.currentRouteData || !this.currentRouteData.points) return;
            var pt = this.currentRouteData.points[index];
            if (!pt) return;

            $('#field-ops-hud-time').text(pt.recorded_at ? pt.recorded_at.substring(11, 19) : '--:--:--');
            $('#field-ops-hud-speed').text((parseFloat(pt.speed) || 0).toFixed(1) + ' km/h');
            $('#field-ops-hud-battery').text(pt.battery_level ? pt.battery_level + '%' : '100%');
            $('#field-ops-hud-status').text(pt.speed > 2.0 ? 'In Transit' : 'Stationary');
        },

        /**
         * Load Real-Time Active Field Ops Grid
         */
        loadLiveFieldOps: function() {
            var self = this;
            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) || '';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_get_live_field_ops',
                    nonce: nonce
                },
                success: function(res) {
                    if (res && res.success) {
                        self.renderLivePersonnel(res.data.personnel || []);
                    }
                }
            });
        },

        renderLivePersonnel: function(personnel) {
            var self = this;
            var $container = $('#field-ops-live-personnel-list');
            $container.empty();

            if (!personnel || personnel.length === 0) {
                $container.html('<div class="p-6 text-center text-xs text-zinc-400 font-medium">No team members currently on an active field shift.</div>');
                return;
            }

            personnel.forEach(function(p) {
                var statusBadge = p.status === 'moving'
                    ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Moving (' + p.speed_kmh + ' km/h)</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">Stationary (' + p.dwell_time + ')</span>';

                var itemHtml = '<div class="p-3 bg-white rounded-xl border border-zinc-200/80 hover:border-zinc-950 transition-all cursor-pointer shadow-xs space-y-2 cora-live-personnel-card" data-userid="' + p.user_id + '">' +
                               '  <div class="flex items-center justify-between gap-2">' +
                               '    <div class="flex items-center gap-2 min-w-0">' +
                               '      <div class="w-7 h-7 rounded-full bg-zinc-950 text-white font-bold text-xs flex items-center justify-center shrink-0">' + (p.name ? p.name.charAt(0).toUpperCase() : 'U') + '</div>' +
                               '      <div class="min-w-0">' +
                               '        <h4 class="text-xs font-bold text-zinc-900 truncate">' + p.name + '</h4>' +
                               '        <span class="text-[10px] text-zinc-400 block">' + p.role + '</span>' +
                               '      </div>' +
                               '    </div>' +
                               '    ' + statusBadge +
                               '  </div>' +
                               '  <div class="flex items-center justify-between text-[11px] text-zinc-500 pt-1 border-t border-zinc-100">' +
                               '    <span>Shift: ' + (p.punch_in_time ? p.punch_in_time.substring(11, 16) : '') + ' (' + p.shift_duration + ')</span>' +
                               '    <span class="font-mono text-[10px] text-zinc-400">Ping: ' + p.last_seen + '</span>' +
                               '  </div>' +
                               '</div>';

                var $el = $(itemHtml);
                $el.on('click', function() {
                    $('#field-ops-user-select').val(p.user_id);
                    self.fetchEmployeeRoute(p.user_id);
                });
                $container.append($el);
            });
        },

        /**
         * Demo Shift Simulator Helper
         * Generates a realistic 6-hour field shift with 3 client visits & 1 lunch rest stop
         */
        simulateDemoShift: function(targetUserId) {
            var self = this;
            var userId = targetUserId || $('#field-ops-user-select').val();
            if (!userId) {
                // Auto-pick first employee in select menu if none explicitly chosen
                var firstOptionVal = $('#field-ops-user-select option[value!=""]:first').val();
                if (firstOptionVal) {
                    userId = firstOptionVal;
                    $('#field-ops-user-select').val(firstOptionVal);
                } else if (window.coraCurrentUserId) {
                    userId = window.coraCurrentUserId;
                }
            }

            if (!userId) {
                if (window.coraShowToast) window.coraShowToast('Please select a team member first to generate a demo shift route.', 'warning');
                return;
            }

            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) || '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) || '';

            // Base coordinates (Bengaluru tech corridor / MG Road to Whitefield)
            var baseLat = 12.9716;
            var baseLng = 77.5946;
            var now = new Date();
            var dateStr = now.toISOString().substring(0, 10);

            var simulatedPoints = [];
            var currTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 9, 0, 0); // 09:00 AM

            // Leg 1: Office Punch In & Departure
            for (var i = 0; i < 6; i++) {
                simulatedPoints.push({
                    punch_id: 9999,
                    lat: baseLat + (i * 0.002),
                    lng: baseLng + (i * 0.003),
                    accuracy: 8,
                    speed: 24.5,
                    heading: 45,
                    battery_level: 98,
                    recorded_at: currTime.toISOString().replace('T', ' ').substring(0, 19)
                });
                currTime.setMinutes(currTime.getMinutes() + 3);
            }

            // Stop 1: Client Site Visit 1 (35 minutes dwell)
            var site1Lat = baseLat + 0.012;
            var site1Lng = baseLng + 0.018;
            for (var j = 0; j < 8; j++) {
                simulatedPoints.push({
                    punch_id: 9999,
                    lat: site1Lat + (Math.random() * 0.0001 - 0.00005),
                    lng: site1Lng + (Math.random() * 0.0001 - 0.00005),
                    accuracy: 5,
                    speed: 0.0,
                    heading: 0,
                    battery_level: 94,
                    recorded_at: currTime.toISOString().replace('T', ' ').substring(0, 19)
                });
                currTime.setMinutes(currTime.getMinutes() + 5);
            }

            // Leg 2: Transit to Lunch Café
            for (var k = 0; k < 5; k++) {
                simulatedPoints.push({
                    punch_id: 9999,
                    lat: site1Lat + (k * 0.003),
                    lng: site1Lng - (k * 0.002),
                    accuracy: 9,
                    speed: 38.0,
                    heading: 280,
                    battery_level: 89,
                    recorded_at: currTime.toISOString().replace('T', ' ').substring(0, 19)
                });
                currTime.setMinutes(currTime.getMinutes() + 4);
            }

            // Stop 2: Lunch & Rest Break (65 minutes dwell)
            var restLat = site1Lat + 0.015;
            var restLng = site1Lng - 0.010;
            for (var m = 0; m < 13; m++) {
                simulatedPoints.push({
                    punch_id: 9999,
                    lat: restLat + (Math.random() * 0.00008 - 0.00004),
                    lng: restLng + (Math.random() * 0.00008 - 0.00004),
                    accuracy: 4,
                    speed: 0.0,
                    heading: 0,
                    battery_level: 82,
                    recorded_at: currTime.toISOString().replace('T', ' ').substring(0, 19)
                });
                currTime.setMinutes(currTime.getMinutes() + 5);
            }

            // Leg 3: Transit to Site 2
            for (var n = 0; n < 7; n++) {
                simulatedPoints.push({
                    punch_id: 9999,
                    lat: restLat - (n * 0.004),
                    lng: restLng + (n * 0.005),
                    accuracy: 10,
                    speed: 42.0,
                    heading: 120,
                    battery_level: 75,
                    recorded_at: currTime.toISOString().replace('T', ' ').substring(0, 19)
                });
                currTime.setMinutes(currTime.getMinutes() + 3);
            }

            // Stop 3: Site Visit 2 (25 minutes dwell)
            var site2Lat = restLat - 0.028;
            var site2Lng = restLng + 0.035;
            for (var p = 0; p < 6; p++) {
                simulatedPoints.push({
                    punch_id: 9999,
                    lat: site2Lat + (Math.random() * 0.0001 - 0.00005),
                    lng: site2Lng + (Math.random() * 0.0001 - 0.00005),
                    accuracy: 6,
                    speed: 0.0,
                    heading: 0,
                    battery_level: 68,
                    recorded_at: currTime.toISOString().replace('T', ' ').substring(0, 19)
                });
                currTime.setMinutes(currTime.getMinutes() + 5);
            }

            if (window.coraShowToast) window.coraShowToast('Synthesizing high-precision multi-stop field telemetry...', 'info');

            // Ingest simulated telemetry into backend
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_sync_gps_telemetry',
                    nonce: nonce,
                    target_user_id: userId,
                    points: JSON.stringify(simulatedPoints),
                    pings: JSON.stringify(simulatedPoints)
                },
                success: function(res) {
                    if (res && res.success) {
                        if (window.coraShowToast) window.coraShowToast('Field shift telemetry generated successfully (' + simulatedPoints.length + ' points). Loading route...', 'success');
                        $('#field-ops-date-select').val(dateStr);
                        self.fetchEmployeeRoute(userId, dateStr);
                    } else {
                        if (window.coraShowToast) window.coraShowToast('Failed to generate demo telemetry: ' + (res.data ? res.data.message : 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    if (window.coraShowToast) window.coraShowToast('Network error while creating demo telemetry.', 'error');
                }
            });
        },

        calculateDistance: function(lat1, lon1, lat2, lon2) {
            var R = 6371e3; // metres
            var φ1 = lat1 * Math.PI / 180;
            var φ2 = lat2 * Math.PI / 180;
            var Δφ = (lat2 - lat1) * Math.PI / 180;
            var Δλ = (lon2 - lon1) * Math.PI / 180;

            var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
    };

    // Auto-init on DOMContentLoaded
    $(document).ready(function() {
        if ($('#tab-field-ops-tracking').length || $('#cora-field-ops-map').length) {
            window.CoraFieldOps.init();
        }
    });

})(window, document, jQuery);
