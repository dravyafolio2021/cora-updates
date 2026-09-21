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
        stopMarkersMap: {},
        currentRouteData: null,
        currentBaseTileLayer: null,
        activeMapStyle: 'streets',
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
        isTouchDevice: false,
        isMapPanActive: false,
        activeSubTab: 'field-ops-panel-map',

        tileProviders: {
            'streets': {
                name: 'Streets (Esri HD)',
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                subdomains: '',
                maxZoom: 19,
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, TomTom'
            },
            'satellite': {
                name: 'Satellite (HD Aerial)',
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                subdomains: '',
                maxZoom: 19,
                attribution: 'Tiles &copy; Esri &mdash; Earthstar Geographics'
            },
            'osm': {
                name: 'Standard OSM',
                url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                subdomains: '',
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            },
            'dark': {
                name: 'Dark Gray Canvas',
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}',
                subdomains: '',
                maxZoom: 16,
                attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ'
            }
        },

        /**
         * Initialize Field Ops Tracker
         */
        init: function() {
            var self = this;
            if (this._initialized) return;
            this._initialized = true;

            this.isTouchDevice = ('ontouchstart' in window || (navigator.maxTouchPoints && navigator.maxTouchPoints > 0));
            this.activeMapStyle = localStorage.getItem('cora_field_ops_map_style') || 'streets';

            // Only initialize Leaflet UI and map layers if map container is in DOM
            if (document.getElementById('cora-field-ops-map') || document.getElementById('tab-field-ops-tracking')) {
                this.ensureLeafletLoaded(function() {
                    self.initMap();
                    self.bindUIEvents();
                    self.loadLiveFieldOps();
                });
            }

            // Start offline queue flush watcher
            setInterval(function() {
                self.flushOfflineQueue();
            }, this.syncIntervalMs);

            // Setup Global Logout Interception to stop tracking & flush final breadcrumb
            this.bindLogoutWatcher();

            // Determine active authenticated employee / user ID
            var userId = (window.coraREData && window.coraREData.currentUserId) || window.coraCurrentUserId || (window.coraCurrentPunchIn && window.coraCurrentPunchIn.id);
            var isLoggedIn = (window.coraREData && window.coraREData.isLoggedIn) || (userId && parseInt(userId, 10) > 0);

            // Auto-start continuous GPS telemetry tracking when employee is logged in
            if (isLoggedIn && userId) {
                this.startEmployeeTracking(userId);
            } else if (window.coraCurrentPunchIn && window.coraCurrentPunchIn.active) {
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
         * Initialize High-Resolution Leaflet Map with Mobile Touch Safety
         */
        initMap: function() {
            var mapContainer = document.getElementById('cora-field-ops-map');
            if (!mapContainer || this.map) return;

            var isDesktop = !this.isTouchDevice && window.innerWidth >= 1024;

            // Initialize map (disable dragging by default on touch screens to allow effortless page scroll)
            this.map = L.map('cora-field-ops-map', {
                center: [12.9716, 77.5946],
                zoom: 13,
                zoomControl: false,
                attributionControl: false,
                dragging: isDesktop,
                touchZoom: isDesktop,
                scrollWheelZoom: isDesktop,
                doubleClickZoom: isDesktop,
                boxZoom: isDesktop,
                keyboard: isDesktop,
                tapHold: false,
                tap: isDesktop
            });

            this.isMapPanActive = isDesktop;
            if (isDesktop) {
                $('#cora-field-ops-map').addClass('cora-map-interactive');
            } else {
                $('#cora-field-ops-map').removeClass('cora-map-interactive');
            }
            this.updateTouchToggleButtonUI();

            // Apply selected high-definition tile provider
            this.setMapStyle(this.activeMapStyle || 'streets', true);

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
         * Switch High-Quality Map Base Layer (100% Free & Zero Watermark)
         */
        setMapStyle: function(styleKey, isInitial) {
            if (!this.tileProviders[styleKey]) styleKey = 'streets';
            this.activeMapStyle = styleKey;
            localStorage.setItem('cora_field_ops_map_style', styleKey);

            var provider = this.tileProviders[styleKey];

            if (this.currentBaseTileLayer && this.map) {
                this.map.removeLayer(this.currentBaseTileLayer);
            }

            if (this.map) {
                var options = {
                    maxZoom: provider.maxZoom || 19,
                    attribution: provider.attribution || ''
                };
                if (provider.subdomains) {
                    options.subdomains = provider.subdomains;
                }

                this.currentBaseTileLayer = L.tileLayer(provider.url, options).addTo(this.map);
                this.currentBaseTileLayer.bringToBack();
            }

            // Update UI buttons
            $('.field-ops-style-btn').removeClass('active bg-zinc-950 text-white shadow-xs').addClass('text-zinc-700');
            $('.field-ops-style-btn[data-style="' + styleKey + '"]').addClass('active bg-zinc-950 text-white shadow-xs').removeClass('text-zinc-700');

            if (!isInitial && window.coraShowToast) {
                window.coraShowToast('Map style updated to ' + provider.name, 'info');
            }
        },

        /**
         * Update Map Pan / Scroll Toggle Button UI
         */
        updateTouchToggleButtonUI: function() {
            var $btn = $('#field-ops-touch-toggle');
            var $text = $('#field-ops-touch-toggle-text');
            if (!$btn.length) return;

            if (this.isMapPanActive) {
                $btn.removeClass('bg-white/95 text-zinc-800').addClass('bg-zinc-950 text-white shadow-lg border-zinc-950');
                $text.text('Lock Map (Scroll Page)');
                $btn.attr('title', 'Map panning is active. Tap to lock map and scroll page.');
            } else {
                $btn.removeClass('bg-zinc-950 text-white shadow-lg border-zinc-950').addClass('bg-white/95 text-zinc-800 shadow-md border-zinc-200/80');
                $text.text('Tap to Pan Map');
                $btn.attr('title', 'Page scroll is active. Tap to pan and zoom map.');
            }
        },

        /**
         * Toggle Map Pan / Page Scroll Mode on Touch Devices
         */
        toggleMapPanMode: function() {
            if (!this.map) return;
            this.isMapPanActive = !this.isMapPanActive;

            if (this.isMapPanActive) {
                $('#cora-field-ops-map').addClass('cora-map-interactive');
                this.map.dragging.enable();
                this.map.touchZoom.enable();
                this.map.scrollWheelZoom.enable();
                if (window.coraShowToast) {
                    window.coraShowToast('Map Pan & Zoom enabled. Tap Lock to scroll page.', 'info');
                }
            } else {
                $('#cora-field-ops-map').removeClass('cora-map-interactive');
                this.map.dragging.disable();
                this.map.touchZoom.disable();
                this.map.scrollWheelZoom.disable();
            }

            this.updateTouchToggleButtonUI();
        },

        /**
         * Universal Sub-Tab Switcher (Route Map, Stops & Legs, Live Crew)
         */
        switchSubTab: function(targetId) {
            if (!targetId) targetId = 'field-ops-panel-map';
            this.activeSubTab = targetId;

            // Update tab button styles
            $('.cora-field-ops-subtab').removeClass('active bg-white text-zinc-950 shadow-xs font-bold').addClass('text-zinc-600 font-semibold');
            $('.cora-field-ops-subtab[data-target="' + targetId + '"]').addClass('active bg-white text-zinc-950 shadow-xs font-bold').removeClass('text-zinc-600 font-semibold');

            // Switch visible panel
            $('.cora-field-ops-panel').addClass('hidden');
            $('#' + targetId).removeClass('hidden');

            if (targetId === 'field-ops-panel-map' && this.map) {
                var self = this;
                setTimeout(function() {
                    if (self.map) self.map.invalidateSize();
                }, 100);
            }
        },

        /**
         * Bind UI Controls
         */
        bindUIEvents: function() {
            var self = this;

            // Universal Segmented Switcher Click
            $(document).on('click', '.cora-field-ops-subtab', function(e) {
                e.preventDefault();
                var target = $(this).attr('data-target');
                self.switchSubTab(target);
            });

            // Map Style Switcher Buttons
            $(document).on('click', '.field-ops-style-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var style = $(this).attr('data-style');
                self.setMapStyle(style);
            });

            // Map Touch/Scroll Mode Toggle Button Click
            $(document).on('click', '#field-ops-touch-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.toggleMapPanMode();
            });

            // Window resize handler
            $(window).on('resize', function() {
                if (self.map) self.map.invalidateSize();
            });

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
         * Real-time GPS Telemetry Watcher for Logged-In Employee
         */
        startEmployeeTracking: function(userId) {
            var self = this;
            if (!userId) {
                userId = (window.coraREData && window.coraREData.currentUserId) || window.coraCurrentUserId;
            }
            if (!userId) return;

            sessionStorage.setItem('cora_employee_tracking_active', '1');
            localStorage.setItem('cora_tracking_user_id', String(userId));

            if (this.watchId !== null) return; // Already watching

            if (!navigator.geolocation) {
                console.warn('[Cora FieldOps] Geolocation is not supported by this device.');
                return;
            }

            console.log('[Cora FieldOps] Telemetry auto-started for employee #' + userId);

            // 1. Initial immediate location ping for login startup
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    self.handlePositionUpdate(pos, 'login_' + userId, 'login_start');
                },
                function(err) {
                    console.warn('[Cora FieldOps] Initial GPS location lookup note:', err.message);
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 }
            );

            // 2. High-accuracy continuous GPS watcher
            var watchOptions = {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 5000
            };

            this.watchId = navigator.geolocation.watchPosition(
                function(pos) {
                    self.handlePositionUpdate(pos, 'session_' + userId, 'active_session');
                },
                function(err) {
                    console.warn('[Cora FieldOps] GPS watch stream note:', err.message);
                },
                watchOptions
            );

            // 3. Heartbeat batch sync timer
            if (!this.timerInterval) {
                this.timerInterval = setInterval(function() {
                    self.flushOfflineQueue();
                }, this.syncIntervalMs);
            }
        },

        startTelemetry: function(punchId) {
            this.startEmployeeTracking(punchId);
        },

        /**
         * Stop Telemetry on Logout / Shift End with Guaranteed Flush
         */
        stopEmployeeTracking: function(isLogout, callback) {
            var self = this;
            sessionStorage.removeItem('cora_employee_tracking_active');
            localStorage.removeItem('cora_tracking_user_id');

            if (this.watchId !== null) {
                navigator.geolocation.clearWatch(this.watchId);
                this.watchId = null;
            }
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }

            if (isLogout && this.lastPosition) {
                var recordedAt = new Date().toISOString().replace('T', ' ').substring(0, 19);
                var logoutPoint = {
                    punch_id: 'logout',
                    lat: this.lastPosition.lat,
                    lng: this.lastPosition.lng,
                    accuracy: this.lastPosition.accuracy || 0,
                    speed: 0,
                    heading: 0,
                    altitude: 0,
                    activity_type: 'logged_out',
                    battery_level: this.lastPosition.battery || 100,
                    recorded_at: recordedAt
                };
                this.queueTelemetry(logoutPoint);
            }

            this.flushOfflineQueue(isLogout);
            console.log('[Cora FieldOps] Telemetry watcher stopped.');
            if (typeof callback === 'function') callback();
        },

        stopTelemetry: function(isLogout) {
            this.stopEmployeeTracking(isLogout);
        },

        /**
         * Bind Global Logout Interception
         */
        bindLogoutWatcher: function() {
            var self = this;
            if (this._logoutBound) return;
            this._logoutBound = true;

            // Intercept all sign-out / logout triggers across the workspace
            $(document).on('click', 'a[href*="action=logout"], a[href*="/workspace/logout"], a[href*="/logout"], .cora-logout-btn, #cora-logout-btn, [data-cora-logout]', function(e) {
                var $link = $(this);
                var href = $link.attr('href');

                // Stop tracking immediately & flush final logout breadcrumb with beacon
                self.stopEmployeeTracking(true);

                if (href && href !== '#' && href.indexOf('javascript:') === -1) {
                    e.preventDefault();
                    setTimeout(function() {
                        window.location.href = href;
                    }, 120);
                }
            });

            // Expose global logout programmatic helper
            window.coraLogout = function(redirectUrl) {
                self.stopEmployeeTracking(true);
                var target = redirectUrl || '/workspace/login?action=logout';
                setTimeout(function() {
                    window.location.href = target;
                }, 120);
            };

            // Window beforeunload fallback
            window.addEventListener('beforeunload', function() {
                self.flushOfflineQueue(true);
            });
        },

        handlePositionUpdate: function(pos, punchId, activityType) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            var accuracy = pos.coords.accuracy || 0;
            var speed = pos.coords.speed !== null && !isNaN(pos.coords.speed) ? pos.coords.speed * 3.6 : 0; // km/h
            var heading = pos.coords.heading || 0;
            var altitude = pos.coords.altitude || 0;
            var recordedAt = new Date(pos.timestamp || Date.now()).toISOString().replace('T', ' ').substring(0, 19);

            // Filter micro-jitter if stationary
            if (this.lastPosition && activityType !== 'login_start' && activityType !== 'logged_out') {
                var dist = this.calculateDistance(this.lastPosition.lat, this.lastPosition.lng, lat, lng);
                if (dist < this.minDistanceMeters && speed < 1.0) {
                    return; // Ignore jitter under 15m when not moving
                }
            }

            this.lastPosition = { lat: lat, lng: lng, accuracy: accuracy, battery: 100 };

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
                activity_type: activityType || (speed > 2.0 ? 'transit' : 'stopped'),
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
                
                // If queue reaches 3 points or online, sync
                if (queue.length >= 3 && navigator.onLine) {
                    this.flushOfflineQueue();
                }
            } catch(e) {
                console.error('[Cora FieldOps] LocalStorage error:', e);
            }
        },

        flushOfflineQueue: function(useBeacon) {
            var self = this;
            var raw = localStorage.getItem(this.offlineQueueKey);
            if (!raw) return;

            var queue = JSON.parse(raw || '[]');
            if (!queue || queue.length === 0) return;

            var ajaxUrl = (window.coraREData && window.coraREData.ajaxUrl) || (window.coraWorkspaceData && window.coraWorkspaceData.ajaxUrl) || '/wp-admin/admin-ajax.php';
            var nonce = (window.coraREData && window.coraREData.ajaxNonce) || (window.coraWorkspaceData && window.coraWorkspaceData.ajaxNonce) || '';
            var targetUserId = (window.coraREData && window.coraREData.currentUserId) || window.coraCurrentUserId || 0;

            if (useBeacon && navigator.sendBeacon) {
                try {
                    var formData = new FormData();
                    formData.append('action', 'cora_sync_gps_telemetry');
                    formData.append('nonce', nonce);
                    if (targetUserId) formData.append('target_user_id', targetUserId);
                    formData.append('points', JSON.stringify(queue));
                    var sent = navigator.sendBeacon(ajaxUrl, formData);
                    if (sent) {
                        localStorage.removeItem(self.offlineQueueKey);
                        return;
                    }
                } catch(e) {
                    console.warn('[Cora FieldOps] Beacon flush fallback to AJAX:', e);
                }
            }

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_sync_gps_telemetry',
                    nonce: nonce,
                    target_user_id: targetUserId,
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
                        self.switchSubTab('field-ops-panel-map');
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
         * Render High-Resolution Map with Vibrant Route Polyline & HD Markers
         */
        renderRouteMap: function(data) {
            var self = this;
            if (!this.map) this.initMap();
            if (!this.map) return;

            this.markersLayer.clearLayers();
            this.routeLayer.clearLayers();
            this.stopMarkersMap = {};

            var points = data.points || [];
            if (points.length === 0) return;

            var latLngs = points.map(function(p) {
                return [parseFloat(p.lat), parseFloat(p.lng)];
            });

            // 1. High-Contrast Outer White Casing (Ensures visibility over both satellite and street tiles)
            L.polyline(latLngs, {
                color: '#ffffff',
                weight: 6,
                opacity: 0.9,
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(this.routeLayer);

            // 2. Vibrant High-Definition Core Route Line (Electric Blue)
            var polyline = L.polyline(latLngs, {
                color: '#2563eb',
                weight: 4,
                opacity: 0.98,
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(this.routeLayer);

            // 3. Subtle glow aura underneath
            L.polyline(latLngs, {
                color: '#3b82f6',
                weight: 10,
                opacity: 0.2,
                lineJoin: 'round'
            }).addTo(this.routeLayer);

            // Fit bounds with padding & invalidate size
            setTimeout(function() {
                if (self.map) {
                    self.map.invalidateSize();
                    self.map.fitBounds(polyline.getBounds(), {
                        padding: [30, 30],
                        maxZoom: 16
                    });
                }
            }, 50);

            // 1. Start Marker (Punch In) - Vibrant Emerald Badge
            var startPt = points[0];
            var startIcon = L.divIcon({
                className: 'cora-custom-map-pin',
                html: '<div class="w-8 h-8 rounded-full bg-emerald-600 border-2 border-white shadow-lg flex items-center justify-center text-white text-[11px] font-bold"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></div>',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
            var startMarker = L.marker([startPt.lat, startPt.lng], { icon: startIcon }).addTo(this.markersLayer);
            startMarker.bindPopup('<div class="p-2.5 text-xs font-sans space-y-1 min-w-[160px]"><div class="font-bold text-emerald-800 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Shift Started (Punch In)</div><div class="text-zinc-500 text-[11px]">' + (startPt.recorded_at || '') + '</div></div>');

            // 2. End / Latest Marker (Punch Out or Live Pin) - Vibrant Rose Badge
            var endPt = points[points.length - 1];
            var endIcon = L.divIcon({
                className: 'cora-custom-map-pin',
                html: '<div class="w-8 h-8 rounded-full bg-rose-600 border-2 border-white shadow-lg flex items-center justify-center text-white text-[11px] font-bold"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg></div>',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
            var endMarker = L.marker([endPt.lat, endPt.lng], { icon: endIcon }).addTo(this.markersLayer);
            endMarker.bindPopup('<div class="p-2.5 text-xs font-sans space-y-1 min-w-[160px]"><div class="font-bold text-rose-800 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-600"></span> Final Recorded Position</div><div class="text-zinc-500 text-[11px]">' + (endPt.recorded_at || '') + '</div></div>');

            // 3. Stop / Rest Markers - Numbered High-Contrast Blue Badges
            var stops = data.stops || [];
            stops.forEach(function(st) {
                var badgeBg = 'bg-blue-600 text-white';
                var stopLabel = 'Quick Stop';
                if (st.type === 'site_visit') {
                    stopLabel = 'Site Visit';
                    badgeBg = 'bg-indigo-600 text-white';
                } else if (st.type === 'rest_break') {
                    stopLabel = 'Rest Break';
                    badgeBg = 'bg-amber-600 text-white';
                }

                var stopIcon = L.divIcon({
                    className: 'cora-stop-map-pin',
                    html: '<div class="relative group cursor-pointer">' +
                          '  <div class="w-6 h-6 rounded-full ' + badgeBg + ' border-2 border-white shadow-md flex items-center justify-center text-[10px] font-extrabold transition-transform group-hover:scale-125">' + st.index + '</div>' +
                          '  <div class="absolute -top-6 left-1/2 -translate-x-1/2 px-1.5 py-0.5 rounded bg-zinc-900 text-white text-[9px] font-mono whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow">' + st.duration_formatted + '</div>' +
                          '</div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                var stopMarker = L.marker([st.lat, st.lng], { icon: stopIcon }).addTo(self.markersLayer);
                var popupHtml = '<div class="p-2.5 text-xs font-sans space-y-1.5 min-w-[180px]">' +
                                '  <div class="flex items-center justify-between gap-2 border-b border-zinc-100 pb-1.5">' +
                                '    <span class="font-bold text-zinc-900">Stop #' + st.index + ' (' + stopLabel + ')</span>' +
                                '    <span class="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold font-mono">' + st.duration_formatted + '</span>' +
                                '  </div>' +
                                '  <div class="grid grid-cols-2 gap-1 text-[11px] text-zinc-600">' +
                                '    <div><span class="text-zinc-400 block text-[9px] uppercase font-bold">Arrived</span> ' + (st.arrival_time ? st.arrival_time.substring(11, 16) : '--:--') + '</div>' +
                                '    <div><span class="text-zinc-400 block text-[9px] uppercase font-bold">Departed</span> ' + (st.departure_time ? st.departure_time.substring(11, 16) : '--:--') + '</div>' +
                                '  </div>' +
                                '  <div class="text-[10px] text-zinc-400 font-mono pt-1">' + parseFloat(st.lat).toFixed(5) + ', ' + parseFloat(st.lng).toFixed(5) + '</div>' +
                                '</div>';
                stopMarker.bindPopup(popupHtml);
                self.stopMarkersMap[st.index] = stopMarker;
            });

            // 4. Setup Replay Avatar Marker - Pulsing Electric Blue Puck
            var replayIcon = L.divIcon({
                className: 'cora-replay-avatar-pin',
                html: '<div class="w-8 h-8 rounded-full bg-blue-600 border-2 border-white shadow-xl flex items-center justify-center text-white ring-4 ring-blue-500/30 animate-pulse">' +
                      '  <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>' +
                      '</div>',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
            this.animMarker = L.marker([startPt.lat, startPt.lng], { icon: replayIcon, zIndexOffset: 1000 }).addTo(this.markersLayer);
        },

        /**
         * Render Summary KPI Cards & Update Sub-tab Badges
         */
        renderSummaryMetrics: function(s) {
            if (!s) return;
            $('#field-ops-kpi-dist').text(s.total_distance_km + ' km');
            $('#field-ops-kpi-transit').text(s.transit_time_formatted || '0m');
            $('#field-ops-kpi-dwell').text(s.dwell_time_formatted || '0m');
            $('#field-ops-kpi-stops').text(s.stop_count || 0);
            $('#field-ops-kpi-speed').text(s.avg_speed_kmh + ' km/h (Max: ' + s.max_speed_kmh + ')');
            $('#field-ops-badge-stops, #field-ops-mobile-badge-stops').text(s.stop_count || 0);
        },

        /**
         * Render Step-by-Step Chronological Leg Timeline with Click-to-Inspect
         */
        renderTimeline: function(legs, stops) {
            var self = this;
            var $list1 = $('#field-ops-timeline-list');
            var $list2 = $('#field-ops-timeline-full-list');
            $list1.empty();
            $list2.empty();

            if (!legs || legs.length === 0) {
                var emptyMsg = '<div class="p-6 text-center text-xs text-zinc-400 font-medium">No shift legs recorded for this session.</div>';
                $list1.html(emptyMsg);
                $list2.html(emptyMsg);
                return;
            }

            legs.forEach(function(leg, idx) {
                var iconHtml = '';
                var title = '';
                var badge = '';
                var subtitle = '';
                var stopNum = null;

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
                    stopNum = (leg.stop_data && leg.stop_data.index) ? leg.stop_data.index : (leg.index || idx);
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

                var itemHtml = '<div class="cora-timeline-leg-item flex items-start gap-3 p-3 rounded-xl border border-zinc-200/80 bg-white hover:border-zinc-950 active:bg-zinc-50 transition-all cursor-pointer shadow-xs w-full" data-stop-index="' + (stopNum || '') + '" data-lat="' + (leg.lat || '') + '" data-lng="' + (leg.lng || '') + '">' +
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

                var bindClick = function($el) {
                    $el.on('click', function() {
                        var sIndex = $(this).attr('data-stop-index');
                        if (sIndex && self.stopMarkersMap[sIndex]) {
                            var marker = self.stopMarkersMap[sIndex];
                            self.switchSubTab('field-ops-panel-map');
                            self.map.panTo(marker.getLatLng(), { animate: true, duration: 0.5 });
                            setTimeout(function() {
                                marker.openPopup();
                            }, 300);
                        }
                    });
                };

                var $item1 = $(itemHtml);
                bindClick($item1);
                $list1.append($item1);

                var $item2 = $(itemHtml);
                bindClick($item2);
                $list2.append($item2);
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
            $('#field-ops-scrubber').val(index);

            var pt = points[index];
            if (this.animMarker && pt) {
                this.animMarker.setLatLng([parseFloat(pt.lat), parseFloat(pt.lng)]);
                if ($('#field-ops-follow-checkbox').is(':checked') && this.map) {
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

            var count = (personnel && personnel.length) ? personnel.length : 0;
            $('#field-ops-badge-crew, #field-ops-mobile-badge-crew').text(count);

            if (!personnel || personnel.length === 0) {
                $container.html('<div class="p-6 text-center text-xs text-zinc-400 font-medium">No team members currently on an active field shift.</div>');
                return;
            }

            personnel.forEach(function(p) {
                var statusBadge = p.status === 'moving'
                    ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Moving (' + p.speed_kmh + ' km/h)</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">Stationary (' + p.dwell_time + ')</span>';

                var itemHtml = '<div class="p-3.5 bg-white rounded-xl border border-zinc-200/80 hover:border-zinc-950 active:bg-zinc-50 transition-all cursor-pointer shadow-xs space-y-2 cora-live-personnel-card" data-userid="' + p.user_id + '">' +
                               '  <div class="flex items-center justify-between gap-2">' +
                               '    <div class="flex items-center gap-2 min-w-0">' +
                               '      <div class="w-8 h-8 rounded-full bg-zinc-950 text-white font-bold text-xs flex items-center justify-center shrink-0">' + (p.name ? p.name.charAt(0).toUpperCase() : 'U') + '</div>' +
                               '      <div class="min-w-0">' +
                               '        <h4 class="text-xs font-bold text-zinc-900 truncate">' + p.name + '</h4>' +
                               '        <span class="text-[10px] text-zinc-400 block">' + p.role + '</span>' +
                               '      </div>' +
                               '    </div>' +
                               '    ' + statusBadge +
                               '  </div>' +
                               '  <div class="flex items-center justify-between text-[11px] text-zinc-500 pt-1.5 border-t border-zinc-100">' +
                               '    <span>Shift: ' + (p.punch_in_time ? p.punch_in_time.substring(11, 16) : '') + ' (' + p.shift_duration + ')</span>' +
                               '    <span class="font-mono text-[10px] text-zinc-400">Ping: ' + p.last_seen + '</span>' +
                               '  </div>' +
                               '</div>';

                var $el = $(itemHtml);
                $el.on('click', function() {
                    $('#field-ops-user-select').val(p.user_id);
                    self.switchSubTab('field-ops-panel-map');
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
                        self.switchSubTab('field-ops-panel-map');
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
        if (window.CoraFieldOps && typeof window.CoraFieldOps.init === 'function') {
            window.CoraFieldOps.init();
        }
    });

})(window, document, jQuery);
