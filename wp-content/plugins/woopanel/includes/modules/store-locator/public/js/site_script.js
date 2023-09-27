asl_jQuery(document).ready(function() {
    var asl_locator = function() {},
        X, Y_pan_mode;
    (window.asl_locator = asl_locator, window.google && google.maps) && (InfoBox.prototype = new google.maps.OverlayView, InfoBox.prototype.createInfoBoxDiv_ = function() {
        var t, e, o, i = this,
            s = function(t) {
                t.cancelBubble = !0, t.stopPropagation && t.stopPropagation()
            };
        if (!this.div_) {
            if (this.div_ = document.createElement("div"), this.setBoxStyle_(), void 0 === this.content_.nodeType ? this.div_.innerHTML = this.getCloseBoxImg_() + this.content_ : (this.div_.innerHTML = this.getCloseBoxImg_(), this.div_.appendChild(this.content_)), this.getPanes()[this.pane_].appendChild(this.div_), this.addClickHandler_(), this.div_.style.width ? this.fixedWidthSet_ = !0 : 0 !== this.maxWidth_ && this.div_.offsetWidth > this.maxWidth_ ? (this.div_.style.width = this.maxWidth_, this.div_.style.overflow = "auto", this.fixedWidthSet_ = !0) : (o = this.getBoxWidths_(), this.div_.style.width = this.div_.offsetWidth - o.left - o.right + "px", this.fixedWidthSet_ = !1), this.panBox_(this.disableAutoPan_), !this.enableEventPropagation_) {
                for (this.eventListeners_ = [], e = ["mousedown", "mouseover", "mouseout", "mouseup", "click", "dblclick", "touchstart", "touchend", "touchmove"], t = 0; t < e.length; t++) this.eventListeners_.push(google.maps.event.addDomListener(this.div_, e[t], s));
                this.eventListeners_.push(google.maps.event.addDomListener(this.div_, "mouseover", function(t) {
                    this.style.cursor = "default"
                }))
            }
            this.contextListener_ = google.maps.event.addDomListener(this.div_, "contextmenu", function(t) {
                t.returnValue = !1, t.preventDefault && t.preventDefault(), i.enableEventPropagation_ || s(t)
            }), google.maps.event.trigger(this, "domready")
        }
    }, InfoBox.prototype.getCloseBoxImg_ = function() {
        var t = "";
        return "" !== this.closeBoxURL_ && (t = "<img", t += " src='" + this.closeBoxURL_ + "'", t += " align=right", t += " style='", t += " position: relative;", t += " cursor: pointer;", t += " margin: " + this.closeBoxMargin_ + ";", t += "'>"), t
    }, InfoBox.prototype.addClickHandler_ = function() {
        var t;
        "" !== this.closeBoxURL_ ? (t = this.div_.firstChild, this.closeListener_ = google.maps.event.addDomListener(t, "click", this.getCloseClickHandler_())) : this.closeListener_ = null
    }, InfoBox.prototype.getCloseClickHandler_ = function() {
        var e = this;
        return function(t) {
            t.cancelBubble = !0, t.stopPropagation && t.stopPropagation(), google.maps.event.trigger(e, "closeclick"), e.close()
        }
    }, InfoBox.prototype.panBox_ = function(t) {
        var e, o = 0,
            i = 0;
        if (!t && (e = this.getMap()) instanceof google.maps.Map) {
            e.getBounds().contains(this.position_) || e.setCenter(this.position_), e.getBounds();
            var s = e.getDiv(),
                a = s.offsetWidth,
                n = s.offsetHeight,
                r = this.pixelOffset_.width,
                l = this.pixelOffset_.height,
                c = this.div_.offsetWidth,
                _ = this.div_.offsetHeight,
                d = this.infoBoxClearance_.width,
                p = this.infoBoxClearance_.height,
                h = this.getProjection().fromLatLngToContainerPixel(this.position_);
            if (h.x < -r + d ? o = h.x + r - d : h.x + c + r + d > a && (o = h.x + c + r + d - a), this.alignBottom_ ? h.y < -l + p + _ ? i = h.y + l - p - _ : h.y + l + p > n && (i = h.y + l + p - n) : h.y < -l + p ? i = h.y + l - p : h.y + _ + l + p > n && (i = h.y + _ + l + p - n), 0 !== o || 0 !== i) {
                e.getCenter();
                e.panBy(o, i)
            }
        }
    }, InfoBox.prototype.setBoxStyle_ = function() {
        var t, e;
        if (this.div_) {
            for (t in this.div_.className = this.boxClass_, this.div_.style.cssText = "", e = this.boxStyle_) e.hasOwnProperty(t) && (this.div_.style[t] = e[t]);
            this.div_.style.WebkitTransform = "translateZ(0)", void 0 !== this.div_.style.opacity && "" !== this.div_.style.opacity && (this.div_.style.MsFilter = '"progid:DXImageTransform.Microsoft.Alpha(Opacity=' + 100 * this.div_.style.opacity + ')"', this.div_.style.filter = "alpha(opacity=" + 100 * this.div_.style.opacity + ")"), this.div_.style.position = "absolute", this.div_.style.visibility = "hidden", null !== this.zIndex_ && (this.div_.style.zIndex = this.zIndex_)
        }
    }, InfoBox.prototype.getBoxWidths_ = function() {
        var t, e = {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0
            },
            o = this.div_;
        return document.defaultView && document.defaultView.getComputedStyle ? (t = o.ownerDocument.defaultView.getComputedStyle(o, "")) && (e.top = parseInt(t.borderTopWidth, 10) || 0, e.bottom = parseInt(t.borderBottomWidth, 10) || 0, e.left = parseInt(t.borderLeftWidth, 10) || 0, e.right = parseInt(t.borderRightWidth, 10) || 0) : document.documentElement.currentStyle && o.currentStyle && (e.top = parseInt(o.currentStyle.borderTopWidth, 10) || 0, e.bottom = parseInt(o.currentStyle.borderBottomWidth, 10) || 0, e.left = parseInt(o.currentStyle.borderLeftWidth, 10) || 0, e.right = parseInt(o.currentStyle.borderRightWidth, 10) || 0), e
    }, InfoBox.prototype.onRemove = function() {
        this.div_ && (this.div_.parentNode.removeChild(this.div_), this.div_ = null)
    }, InfoBox.prototype.draw = function() {
        this.createInfoBoxDiv_();
        var t = this.getProjection().fromLatLngToDivPixel(this.position_);
        this.div_.style.left = t.x + this.pixelOffset_.width + "px", this.alignBottom_ ? this.div_.style.bottom = -(t.y + this.pixelOffset_.height) + "px" : this.div_.style.top = t.y + this.pixelOffset_.height + "px", this.isHidden_ ? this.div_.style.visibility = "hidden" : this.div_.style.visibility = "visible"
    }, InfoBox.prototype.setOptions = function(t) {
        void 0 !== t.boxClass && (this.boxClass_ = t.boxClass, this.setBoxStyle_()), void 0 !== t.boxStyle && (this.boxStyle_ = t.boxStyle, this.setBoxStyle_()), void 0 !== t.content && this.setContent(t.content), void 0 !== t.disableAutoPan && (this.disableAutoPan_ = t.disableAutoPan), void 0 !== t.maxWidth && (this.maxWidth_ = t.maxWidth), void 0 !== t.pixelOffset && (this.pixelOffset_ = t.pixelOffset), void 0 !== t.alignBottom && (this.alignBottom_ = t.alignBottom), void 0 !== t.position && this.setPosition(t.position), void 0 !== t.zIndex && this.setZIndex(t.zIndex), void 0 !== t.closeBoxMargin && (this.closeBoxMargin_ = t.closeBoxMargin), void 0 !== t.closeBoxURL && (this.closeBoxURL_ = t.closeBoxURL), void 0 !== t.infoBoxClearance && (this.infoBoxClearance_ = t.infoBoxClearance), void 0 !== t.isHidden && (this.isHidden_ = t.isHidden), void 0 !== t.visible && (this.isHidden_ = !t.visible), void 0 !== t.enableEventPropagation && (this.enableEventPropagation_ = t.enableEventPropagation), this.div_ && this.draw()
    }, InfoBox.prototype.setContent = function(t) {
        this.content_ = t, this.div_ && (this.closeListener_ && (google.maps.event.removeListener(this.closeListener_), this.closeListener_ = null), this.fixedWidthSet_ || (this.div_.style.width = ""), void 0 === t.nodeType ? this.div_.innerHTML = this.getCloseBoxImg_() + t : (this.div_.innerHTML = this.getCloseBoxImg_(), this.div_.appendChild(t)), this.fixedWidthSet_ || (this.div_.style.width = this.div_.offsetWidth + "px", void 0 === t.nodeType ? this.div_.innerHTML = this.getCloseBoxImg_() + t : (this.div_.innerHTML = this.getCloseBoxImg_(), this.div_.appendChild(t))), this.addClickHandler_()), google.maps.event.trigger(this, "content_changed")
    }, InfoBox.prototype.setPosition = function(t) {
        this.position_ = t, this.div_ && this.draw(), google.maps.event.trigger(this, "position_changed")
    }, InfoBox.prototype.setZIndex = function(t) {
        this.zIndex_ = t, this.div_ && (this.div_.style.zIndex = t), google.maps.event.trigger(this, "zindex_changed")
    }, InfoBox.prototype.setVisible = function(t) {
        this.isHidden_ = !t, this.div_ && (this.div_.style.visibility = this.isHidden_ ? "hidden" : "visible")
    }, InfoBox.prototype.getContent = function() {
        return this.content_
    }, InfoBox.prototype.getPosition = function() {
        return this.position_
    }, InfoBox.prototype.getZIndex = function() {
        return this.zIndex_
    }, InfoBox.prototype.getVisible = function() {
        return void 0 !== this.getMap() && null !== this.getMap() && !this.isHidden_
    }, InfoBox.prototype.show = function() {
        this.isHidden_ = !1, this.div_ && (this.div_.style.visibility = "visible")
    }, InfoBox.prototype.hide = function() {
        this.isHidden_ = !0, this.div_ && (this.div_.style.visibility = "hidden")
    }, InfoBox.prototype.open = function(t, e) {
        var o = this;
        e && (this.position_ = e.getPosition(), this.moveListener_ = google.maps.event.addListener(e, "position_changed", function() {
            o.setPosition(this.getPosition())
        })), this.setMap(t), this.div_ && this.panBox_()
    }, InfoBox.prototype.close = function() {
        var t;
        if (this.closeListener_ && (google.maps.event.removeListener(this.closeListener_), this.closeListener_ = null), this.eventListeners_) {
            for (t = 0; t < this.eventListeners_.length; t++) google.maps.event.removeListener(this.eventListeners_[t]);
            this.eventListeners_ = null
        }
        this.moveListener_ && (google.maps.event.removeListener(this.moveListener_), this.moveListener_ = null), this.contextListener_ && (google.maps.event.removeListener(this.contextListener_), this.contextListener_ = null), this.setMap(null)
    }, X = asl_jQuery, Y_pan_mode = !1, asl_locator.toRad_ = function(t) {
        return t * Math.PI / 180
    }, asl_locator.Store = function(t, e, o, i) {
        this.id_ = t, this.location_ = e, this.categories_ = o, this.props_ = i || {}, this.v_id = i.vendor_id
    }, asl_locator.Store.prototype.setMarker = function(t) {
        this.marker_ = t, google.maps.event.trigger(this, "marker_changed", t)
    }, asl_locator.Store.prototype.getMarker = function() {
        return this.marker_
    }, asl_locator.Store.prototype.getId = function() {
        return this.id_
    }, asl_locator.Store.prototype.getLocation = function() {
        return this.location_
    }, asl_locator.Store.prototype.hasCategory = function(t) {
        return -1 != this.categories_.indexOf(t)
    }, asl_locator.Store.prototype.hasAnyCategory = function(t) {
        if (!t.array_.length) return !0;
        for (var e = t.asList(), o = 0, i = e.length; o < i; o++)
            if (-1 != this.categories_.indexOf(e[o].id_)) return !0;
        return !1
    }, asl_locator.Store.prototype.getDetails = function() {
        return this.props_
    }, asl_locator.Store.prototype.generateFieldsHTML_ = function(t) {
        for (var e = [], o = 0, i = t.length; o < i; o++) {
            var s = t[o];
            this.props_[s] && (e.push('<div class="'), e.push(s), e.push('">'), e.push(s + ": "), e.push(isNaN(this.props_[s]) ? this.props_[s] : numberWithCommas(this.props_[s])), e.push("</div>"))
        }
        return e.join("")
    }, asl_locator.Store.prototype.generateFeaturesHTML_ = function() {
        var t = [];
        t.push('<ul class="features">');
        for (var e, o = this.categories_.asList(), i = 0; e = o[i]; i++) t.push("<li>"), t.push(e.getDisplayName()), t.push("</li>");
        return t.push("</ul>"), t.join("")
    }, asl_locator.Store.prototype.getStoreContent = function() {
        if (!this.content_) {
            var t = window.asl_tmpl_list_item ? window.asl_tmpl_list_item : X.templates(window.asl_info_list || "#tmpl_list_item");
            window.asl_tmpl_list_item = t, this.content_ = X(t.render(this.props_))
        }
        return this.content_
    }, asl_locator.Store.prototype.getcontent_ = function(t) {
        var e = window.asl_too_tip_tmpl ? window.asl_too_tip_tmpl : X.templates(window.asl_info_box || "#asl_too_tip");
        return window.asl_too_tip_tmpl = e, t.props_.show_categories = asl_configuration.show_categories, t.props_.URL = asl_configuration.URL, e.render(t.props_)
    }, asl_locator.Store.prototype.getInfoWindowContent = function(t) {
        var e = '<div class="infoWindow xxxx" id="style_0">';
        return e += this.getcontent_(this), e += "</div>", this.content_ = e, this.content_
    }, asl_locator.Store.infoPanelCache_ = {}, asl_locator.Store.prototype.getInfoPanelItem = function() {
        var t = asl_locator.Store.infoPanelCache_,
            e = this.id_;
        if (!t[e]) {
            var o = this.getStoreContent();
            t[e] = o[0]
        }
        return t[e]
    }, asl_locator.Store.prototype.distanceTo = function(t) {
        var e = this.getLocation(),
            o = asl_locator.toRad_(e.lat()),
            i = asl_locator.toRad_(e.lng()),
            s = asl_locator.toRad_(t.lat()),
            a = s - o,
            n = asl_locator.toRad_(t.lng()) - i,
            r = Math.sin(a / 2) * Math.sin(a / 2) + Math.cos(o) * Math.cos(s) * Math.sin(n / 2) * Math.sin(n / 2),
            l = 2 * Math.atan2(Math.sqrt(r), Math.sqrt(1 - r)) * 6371;
        return "MILES" == asl_configuration.distance_unit ? .621371 * l : l
    }, asl_locator.View = function(t, e, o) {
        this.map_ = t, this.data_ = e, this.settings_ = X.extend({
            updateOnPan: !0,
            geolocation: !1,
            features: new asl_locator.FeatureSet
        }, o), this.init_(), google.maps.event.trigger(this, "load"), this.set("featureFilter", new asl_locator.FeatureSet), this.active_marker = {
            m: null,
            picon: null,
            icon: new google.maps.MarkerImage(asl_configuration.URL + "public/icon/active.png", null, null)
        }
    }, asl_locator.View = asl_locator.View, asl_locator.View.prototype = new google.maps.MVCObject, asl_locator.View.prototype.measure_distance = function(t, e, o) {
        var i = this,
            s = new google.maps.LatLng(t.lat(), t.lng());
        i._panel.dest_coords = i.dest_coords = s;
        var a = asl_configuration.radius_range;
        for (var n in i.data_.stores_)
            if (i.data_.stores_.hasOwnProperty(n)) {
                var r = i.data_.stores_[n].distanceTo(i.dest_coords);
                i.data_.stores_[n].content_ = null, i.data_.stores_[n].props_.distance = r, i.data_.stores_[n].props_.dist_str = r.toFixed(2) + " " + asl_configuration.distance_unit, a < r && (a = r)
            } if (asl_configuration.radius_range = asl_configuration.fixed_radius ? parseInt(asl_configuration.fixed_radius) : Math.round(a), delete asl_locator.Store.infoPanelCache_, asl_locator.Store.infoPanelCache_ = {}, i.my_marker) i.my_marker.setPosition(s);
        else {
            i.my_marker = new google.maps.Marker({
                title: "Your Current Location",
                position: s,
                animation: google.maps.Animation.DROP,
                draggable: !0,
                map: i.getMap()
            });
            var l = new google.maps.MarkerImage(asl_configuration.URL + "public/img/me-pin.png", null, null, null);
            i.my_marker.setIcon(l), i.my_marker.addListener("dragend", function(t) {
                i.measure_distance(t.latLng)
            })
        }
        o || (i.getMap().setCenter(s), i.getMap().setZoom(parseInt(asl_configuration.zoom)), google.maps.event.trigger(i, "load"), i.refreshView(!0))
    }, asl_locator.View.prototype.geolocate_ = function() {
        var e = this;
        window.navigator && navigator.geolocation && navigator.geolocation.getCurrentPosition(function(t) {
            e.measure_distance(new google.maps.LatLng(t.coords.latitude, t.coords.longitude))
        }, void 0, {
            maximumAge: 6e4,
            timeout: 1e4
        })
    }, asl_locator.View.prototype.init_ = function() {
        this.settings_.geolocation && this.geolocate_(), this.markerCache_ = {}, this.infoWindow_ = new InfoBox({
            boxStyle: {
                width: "250px",
                margin: "0 0 33px -120px"
            },
            alignBottom: !0,
            pane: !1,
            disableAutoPan: !0,
            closeBoxMargin: "12px 4px -20px 0",
            closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
            infoBoxClearance: new google.maps.Size(1, 1)
        });
        var t = this,
            e = this.getMap();
        this.set("updateOnPan", this.settings_.updateOnPan), google.maps.event.addListener(this.infoWindow_, "closeclick", function() {
            t.highlight(null)
        }), google.maps.event.addListener(e, "click", function() {
            t.highlight(null), t.infoWindow_.close()
        })
    }, asl_locator.View.prototype.updateOnPan_changed = function() {
        this.updateOnPanListener_ && google.maps.event.removeListener(this.updateOnPanListener_);
        var t = this;
        if (this.get("updateOnPan") && this.getMap()) {
            var e = (t = this).getMap();
            this.updateOnPanListener_ = google.maps.event.addListener(e, "idle", function() {
                !t.showing_direction && asl_configuration.load_all
            })
        }
    }, asl_locator.View.prototype.addStoreToMap = function(t) {
        var e = this.getMarker(t);
        t.setMarker(e);
        var o = this;
        e.clickListener_ = google.maps.event.addListener(e, "click", function() {
            o.marker_clicked = !0, o.marker_center = e.getPosition(), o.highlight(t, !1), e.setAnimation(google.maps.Animation.Xp)
        }), e.getMap() != this.getMap() && (e.setMap(this.getMap()), e.setAnimation(google.maps.Animation.Xp))
    }, asl_locator.View.prototype.createMarker = function(t) {
        var e = asl_configuration.URL + "public/icon/";
        return e += "default.png", new google.maps.Marker({
            position: t.getLocation(),
            animation: null,
            title: t.props_.title,
            icon: new google.maps.MarkerImage(e, null, null, null, null)
        })
    }, asl_locator.View.prototype.getMarker = function(t) {
        var e = this.markerCache_,
            o = t.id_;
        return e[o] || (e[o] = this.createMarker(t)), e[o]
    }, asl_locator.View.prototype.getInfoWindow = function(t, e) {
        if (!t) return this.infoWindow_;
        var o = X(t.getInfoWindowContent(e));

        return this.infoWindow_.setContent(o[0]), this.infoWindow_
    }, asl_locator.View.prototype.getViewFeatures = function() {
        return this.settings_.features
    }, asl_locator.View.prototype.getFeatureById = function(t) {
        if (!this.featureById_) {
            this.featureById_ = {};
            for (var e, o = 0; e = this.settings_.features[o]; o++) this.featureById_[e.id_] = e
        }
        return this.featureById_[t]
    }, asl_locator.View.prototype.featureFilter_changed = function() {
        google.maps.event.trigger(this, "featureFilter_changed", this.get("featureFilter")), this.get("stores") && this.clearMarkers()
    }, asl_locator.View.prototype.clearMarkers = function() {
        for (var t in this.markerCache_) {
            this.markerCache_[t].setMap(null);
            var e = this.markerCache_[t].clickListener_;
            e && google.maps.event.removeListener(e)
        }
    }, asl_locator.View.prototype.refreshView = function(t) {
        var l = this;
        this.data_ = window.asl_data_source;

        console.log("CALLING REFRESH VIEW"),
        this.data_.getStores(this.getMap().getBounds(), this.get("featureFilter"), function(t) {
            var e = l.get("stores");

            if (e)
                for (var o = 0, i = e.length; o < i; o++) google.maps.event.removeListener(e[o].getMarker().clickListener_);
            var s = [],
                a = Object.keys(asl_categories);
            for (var n in a) asl_categories[a[n]] && (asl_categories[a[n]].len = 0);
            for (var r in t)
                if (t.hasOwnProperty(r)) {
                    for (var n in t[r].categories_) t[r].categories_.hasOwnProperty(n) && asl_categories[t[r].categories_[n]] && asl_categories[t[r].categories_[n]].len++;
                    s.push(t[r])
                } l.set("stores", s)
        }, t)
    }, asl_locator.View.prototype.stores_changed = function() {
        for (var t, e = this.get("stores"), o = [], i = 0; t = e[i]; i++) this.addStoreToMap(t), o.push(t.marker_);
        "1" == asl_configuration.cluster && (asl_locator.marker_clusters.clearMarkers(), asl_locator.marker_clusters.addMarkers(o))
    }, asl_locator.View.prototype.getMap = function() {
        return this.map_
    }, asl_locator.View.prototype.highlight = function(t, e) {

        var o = null,
            i = this.getMap();

        if (asl_configuration.mobile_optimize && this.current_m && (this.current_m.setMap(null), this.current_m = null), t) {
            var s = this.get("stores");

            if (o = this.getInfoWindow(t, s), !e) {
                console.log("===> site_script.js ===> 752");
                var a = X('.asl-p-cont .item[data-id="' + t.id_ + '"]');
                a[0] && X("#asl-storelocator #panel").animate({
                    scrollTop: a.position().top
                }, "fast")
            }

            if (t.getMarker() ? (t.getMarker(), o.open(i, t.getMarker()), asl_configuration.analytics && asl_locator.save_analytics(t, 1)) : (asl_configuration.mobile_optimize && (this.current_m = this.createMarker(t), this.current_m.setMap(i)), o.setPosition(t.getLocation()), o.open(i)), asl_configuration.smooth_pan) {
                var n = this._overlay.getProjection().fromLatLngToContainerPixel(t.getLocation());
                i.panBy(n.x - this.map_w / 2, n.y - this.map_h / 2)
            } else i.setZoom(parseInt(asl_configuration.zoom_li)), i.panTo(t.getLocation());
            i.getStreetView().getVisible() && i.getStreetView().setPosition(t.getLocation())
        } else this.getInfoWindow().close();
        this.set("selectedStore", t)
    }, asl_locator.View.prototype.selectedStore_changed = function() {
        google.maps.event.trigger(this, "selectedStore_changed", this.get("selectedStore"))
    }, asl_locator.ViewOptions = function() {}, asl_locator.ViewOptions.prototype.updateOnPan, asl_locator.ViewOptions.prototype.geolocation, asl_locator.ViewOptions.prototype.features, asl_locator.ViewOptions.prototype.markerIcon, asl_locator.Feature = function(t, e, o, i) {
        this.id_ = t, this.name_ = e, this.img_ = o, this.s = i
    }, asl_locator.Feature = asl_locator.Feature, asl_locator.Feature.prototype.getId = function() {
        return this.id_
    }, asl_locator.Feature.prototype.getDisplayName = function() {
        return this.name_
    }, asl_locator.Feature.prototype.toString = function() {
        return this.getDisplayName()
    }, asl_locator.FeatureSet = function(t) {
        this.array_ = [], this.hash_ = {};
        for (var e, o = 0; e = arguments[o]; o++) this.add(e)
    }, asl_locator.FeatureSet = asl_locator.FeatureSet, asl_locator.FeatureSet.prototype.toggle = function(t) {
        this.hash_[t.id_] ? this.remove(t) : this.add(t)
    }, asl_locator.FeatureSet.prototype.add = function(t) {
        t && (this.array_.push(t), this.hash_[t.id_] = 1)
    }, asl_locator.FeatureSet.prototype.remove = function(t) {
        var e = t.id_;
        this.hash_[e] && (delete this.hash_[e], this.array_ = this.array_.filter(function(t) {
            return t && t.id_ != e
        }))
    }, asl_locator.FeatureSet.prototype.asList = function() {
        for (var t = [], e = 0, o = this.array_.length; e < o; e++) {
            var i = this.array_[e];
            null !== i && t.push(i)
        }
        return t
    }, asl_locator.FeatureSet.NONE = new asl_locator.FeatureSet, asl_locator.Panel = function(t, e) {
        this.el_ = X(t), this.el_.addClass("asl_locator-panel"), this.settings_ = X.extend({
            locationSearch: !0,
            locationSearchLabel: "Enter Location/ZipCode: ",
            featureFilter: !0,
            directions: !0,
            view: null
        }, e), this.directionsRenderer_ = new google.maps.DirectionsRenderer({
            draggable: !0
        }), this.directionsService_ = new google.maps.DirectionsService, this.init_()
    }, asl_locator.Panel = asl_locator.Panel, asl_locator.Panel.prototype = new google.maps.MVCObject, asl_locator.Panel.prototype.init_ = function() {
        var i = this;
        this.itemCache_ = {}, this.settings_.view && this.set("view", this.settings_.view), this.filter_ = X(".asl-p-cont .header-search");
        var t = i.get("view").getMap();
        window.asl_map = t, "1" == asl_configuration.cluster && (asl_locator.marker_clusters = new MarkerClusterer(t, [], {
            maxZoom: 7,
            gridSize: 40,
            imagePath: asl_configuration.URL + "public/icon/m"
        })), this.settings_.locationSearch && (this.locationSearch_ = this.filter_, void 0 !== google.maps.places ? "1" != asl_configuration.search_type && this.initAutocomplete_() : this.filter_.submit(function() {
            i.searchPosition(X("input", i.locationSearch_).val())
        }), this.filter_.submit(function() {
            return !1
        }), google.maps.event.addListener(this, "geocode", function(t) {
            if (i.searchPositionTimeout_ && window.clearTimeout(i.searchPositionTimeout_), t.geometry) {
                this.directionsFrom_ = t.geometry.location, i.directionsVisible_ && i.renderDirections_();
                var e = i.get("view");
                e.highlight(null);
                var o = e.getMap();
                t.geometry.viewport ? o.fitBounds(t.geometry.viewport) : (o.setCenter(t.geometry.location), o.setZoom(parseInt(asl_configuration.zoom_li))), e.refreshView(), i.listenForStoresUpdate_()
            } else i.searchPosition(t.name)
        })), this.settings_.featureFilter && (this.featureFilter_ = X(".asl-p-cont #filter-options"), this.featureFilter_.show(), asl_configuration.show_categories || X(".asl-p-cont .drop_box_filter").remove(), asl_configuration.radius_range || (asl_configuration.radius_range = asl_configuration.fixed_radius ? parseInt(asl_configuration.fixed_radius) : 1e3), this.get("view").getViewFeatures().asList(), this.featureFilter_.find(".inner-filter"), this.storeList_ = X("#wpl-seller-panel")), this.directionsPanel_ = X("#wpl-store-modal-direction");
        var e = this.directionsPanel_.find(".frm-place");
        e.val(""), i.dest_coords && (s.directionsFrom_ = i.dest_coords);
        var o = this.directionsPanel_.find(".frm-place")[0];
        this.input_search = new google.maps.places.Autocomplete(o);
        var s = this;
        google.maps.event.addListener(this.input_search, "place_changed", function() {
            s.directionsFrom_ = this.getPlace().geometry.location
        }), this.directionsPanel_.find(".directions-to").attr("readonly", "readonly"), this.directionsVisible_ = !1, this.directionsPanel_.find(".btn-submit").click(function(t) {
            return i.dest_coords && "Current Location" == e.val() && (i.directionsFrom_ = i.dest_coords || null), i.renderDirections_(), !1
        }), "KM" == asl_configuration.distance_unit ? (i.distance_type = google.maps.UnitSystem.METRIC, i.directionsPanel_.find("#rbtn-km")[0].checked = !0) : i.distance_type = google.maps.UnitSystem.IMPERIAL, i.directionsPanel_.find("input[name=dist-type]").change(function() {
            i.distance_type = 1 == this.value ? google.maps.UnitSystem.IMPERIAL : google.maps.UnitSystem.METRIC
        }), this.el_.find(".directions-cont .close").click(function() {
            i.hideDirections(), X(".asl-p-cont .count-row").removeClass("hide"), X(".asl-p-cont #filter-options").removeClass("hide")
        }), this.directionsPanel_.find(".close-directions").click(function() {
            i.hideDirections(), X(".asl-p-cont .count-row").removeClass("hide"), X(".asl-p-cont #filter-options").removeClass("hide")
        });
    }, asl_locator.Panel.prototype.toggleFeatureFilter_ = function(t) {
        var e = this.get("featureFilter");
        e.toggle(t), this.set("featureFilter", e)
    }, asl_locator.geocoder_ = new google.maps.Geocoder, asl_locator.Panel.prototype.listenForStoresUpdate_ = function() {
        var t = this,
            e = this.get("view");
        this.storesChangedListener_ && google.maps.event.removeListener(this.storesChangedListener_), this.storesChangedListener_ = google.maps.event.addListenerOnce(e, "stores_changed", function() {
            t.set("stores", e.get("stores"))
        })
    }, asl_locator.Panel.prototype.searchPosition = function(t) {
        var o = this,
            e = {
                address: t,
                bounds: this.get("view").getMap().getBounds()
            };
        asl_locator.geocoder_.geocode(e, function(t, e) {
            e == google.maps.GeocoderStatus.OK && google.maps.event.trigger(o, "geocode", t[0])
        })
    }, asl_locator.Panel.prototype.setView = function(t) {
        this.set("view", t)
    }, asl_locator.Panel.prototype.view_changed = function() {
        var t = this,
            e = this.get("view");
        this.bindTo("selectedStore", e), this.geolocationListener_ && google.maps.event.removeListener(this.geolocationListener_), this.zoomListener_ && google.maps.event.removeListener(this.zoomListener_), this.idleListener_ && google.maps.event.removeListener(this.idleListener_), e.getMap().getCenter();
        var o = function() {
            Y_pan_mode || t.listenForStoresUpdate_()
        };
        this.geolocationListener_ = google.maps.event.addListener(e, "load", o), this.zoomListener_ = google.maps.event.addListener(e.getMap(), "zoom_changed", o), this.idleListener_ = google.maps.event.addListener(e.getMap(), "idle", function() {
            return t.idle_(e.getMap())
        }), o(), this.bindTo("featureFilter", e), this.autoComplete_ && this.autoComplete_.bindTo("bounds", e.getMap())
    }, asl_locator.Panel.prototype.geoCoder = function(t, e) {
        var o = this,
            i = new google.maps.Geocoder;
        e = e || function(t, e) {
            "OK" == e && o.get("view").measure_distance(t[0].geometry.location, !0)
        }, X(t).bind("keyup", function(t) {
            13 == t.keyCode && i.geocode({
                address: this.value
            }, e)
        })
    }, asl_locator.Panel.prototype.initAutocomplete_ = function() {
        var e = this,
            t = X("#wpl-store-auto-complete")[0],
            o = {};
        asl_configuration.google_search_type && (o.types = "cities" == asl_configuration.google_search_type || "regions" == asl_configuration.google_search_type ? ["(" + asl_configuration.google_search_type + ")"] : [asl_configuration.google_search_type]), asl_configuration.country_restrict && (o.componentRestrictions = {
            country: asl_configuration.country_restrict.toLowerCase()
        }), this.autoComplete_ = new google.maps.places.Autocomplete(t, o), this.get("view") && this.autoComplete_.bindTo("bounds", this.get("view").getMap()), asl_configuration.enter_key && e.geoCoder(t), google.maps.event.addListener(this.autoComplete_, "place_changed", function() {
            var t = this.getPlace();
            t.geometry && e.get("view").measure_distance(t.geometry.location, !0)
        })
    }, asl_locator.Panel.prototype.idle_ = function(t) {
        this.center_ ? t.getBounds().contains(this.center_) || (this.center_ = t.getCenter(), this.listenForStoresUpdate_()) : this.center_ = t.getCenter()
    }, asl_locator.Panel.prototype.stores_changed = function() {
        if (this.get("stores")) {
            var o = this,
                e = this.get("view");
            if (!e.showing_direction) {
                e.is_updated = !0;
                var t = e && e.getMap().getBounds(),
                    i = e.get("stores"),
                    s = this.get("selectedStore");
                this.storeList_.empty(), i.length ? (t && t.contains(i[0].getLocation()), X(".asl-p-cont .Num_of_store .count-result").html(i.length)) : (X(".asl-p-cont .Num_of_store .count-result").html("0"), o.storeList_.html('<div class="asl-overlay-on-item" id="asl-no-item-found"><div class="white"></div><h1 class="h1">' + asl_configuration.no_item_text + "</h1></div>"));
                for (var a = function(t) {
                        X(t.target).hasClass("s-direction") ? t.preventDefault() : "A" != t.target.tagName && (e.noRefreshList = !0, e.highlight(this.store, !0))
                    }, n = 0, r = i.length; n < r; n++) {
                    var l = i[n].getInfoPanelItem();
                    l.store = i[n], s && i[n].id_ == s.id_ && X(l).addClass("highlighted"), l.clickHandler_ || (l.clickHandler_ = google.maps.event.addDomListener(l, "click", a)), X(l).find(".s-direction").click(function(t) {
                        var e = X(this).data("_store");
                        o.directionsTo_ = e, o.showDirections(e)
                    }).data("_store", i[n]), o.storeList_.append(l)
                }
            }
        }
    }, asl_locator.Panel.prototype.selectedStore_changed = function() {
        X(".highlighted", this.storeList_).removeClass("highlighted");
        var t = this,
            e = this.get("selectedStore"),
            o = t.get("view");

        if (o.active_marker && o.active_marker.m && (o.active_marker.m.setIcon(o.active_marker.picon), o.active_marker.m = null), e) {

            var i = e.getMarker();
            o.active_marker && (o.active_marker.picon = i.getIcon(), (o.active_marker.m = i).setIcon(o.active_marker.icon)), this.directionsTo_ = e, this.storeList_.find('div[data-id="' + e.id_ + '"]').addClass("highlighted"), this.settings_.directions && this.directionsPanel_.find(".directions-to").val(e.getDetails().title);
            var s = t.get("view").getInfoWindow().getContent(),
                a = X("<a/>").text(asl_configuration.words.direction).attr("href", "javascript:void(0)").addClass("action").addClass("directions"),
                n = X("<a/>").text(asl_configuration.words.zoom).attr("href", "javascript:void(0)").addClass("action").addClass("zoomhere"),
                r = e.props_.website;

            if (a.click(function() {
                    return t.showDirections(), !1
                }), n.click(function() {
                    t.get("view").getMap().setOptions({
                        center: e.getLocation(),
                        zoom: asl_map.getZoom() + 1
                    })
                }), X(s).find(".asl-buttons").append(a).append(n), r) {
                var l = X("<a/>").text(asl_configuration.words.detail).attr("href", r).addClass("action").addClass("a-visit-store");
                "1" == asl_configuration.target_blank && l.attr("target", "_blank"), X(s).find(".asl-buttons").append(l)
            }
        }
    }, asl_locator.Panel.prototype.hideDirections = function() {
        this.directionsVisible_ = !1, this.directionsPanel_.removeClass("in"), this.el_.find(".directions-cont").addClass("hide"), this.storeList_.fadeIn(), this.directionsRenderer_.setMap(null), this.get("view").showing_direction = !1
    }, asl_locator.Panel.prototype.showDirections = function(t) {
        var e = this.get("selectedStore") || t;
        if (e) {
            if (_isMobileDevice()) {
                var o = "https://www.google.com/maps/dir/Current+Location/" + e.location_.lat() + "," + e.location_.lng() + "?hl=en";
                return window.open(o, "_blank"), !0
            }
            this.directionsPanel_.find(".frm-place").val(this.dest_coords ? "Current Location" : ""), this.directionsPanel_.find(".directions-to").val(e.getDetails().title), this.directionsPanel_.addClass("in"), this.renderDirections_(), this.directionsVisible_ = !0
        }
    }, asl_locator.Panel.prototype.renderDirections_ = function() {
        var i = this;
        if (this.directionsFrom_ && this.directionsTo_) {
            this.el_.find("#map-loading").show(), this.el_.find(".directions-cont").removeClass("hide"), this.storeList_.fadeOut(), i.directionsPanel_.removeClass("in");
            var s = this.el_.find(".rendered-directions").empty();
            this.directionsService_.route({
                origin: this.directionsFrom_,
                destination: this.directionsTo_.getLocation(),
                travelMode: google.maps.DirectionsTravelMode.DRIVING,
                unitSystem: i.distance_type
            }, function(t, e) {
                if (i.el_.find("#map-loading").hide(), e == google.maps.DirectionsStatus.OK) {
                    X(".asl-p-cont .count-row").addClass("hide"), X(".asl-p-cont #filter-options").addClass("hide"), i.get("view").showing_direction = !0;
                    var o = i.directionsRenderer_;
                    o.setPanel(s[0]), o.setMap(i.get("view").getMap()), o.setDirections(t)
                }
            }), this.directionsFrom_ = null
        }
    }, asl_locator.Panel.prototype.featureFilter_changed = function() {
        this.listenForStoresUpdate_()
    }, asl_locator.PanelOptions = function() {}, asl_locator.prototype.locationSearch, asl_locator.PanelOptions.prototype.locationSearchLabel, asl_locator.PanelOptions.prototype.featureFilter, asl_locator.PanelOptions.prototype.directions, asl_locator.PanelOptions.prototype.view, function($, _) {
        var map = null,
            asl_engine = {
                config: {},
                helper: {}
            };
        if (window.asl_engine = asl_engine, window.asl_configuration) {
            asl_configuration.accordion = "1" == asl_configuration.layout, asl_configuration.analytics = "1" == asl_configuration.analytics, asl_configuration.sort_by_bound = "1" == asl_configuration.sort_by_bound, asl_configuration.scroll_wheel = "1" == asl_configuration.scroll_wheel, asl_configuration.show_categories = "0" != asl_configuration.show_categories, asl_configuration.category_marker = "0" != asl_configuration.category_marker, asl_configuration.advance_filter = "0" != asl_configuration.advance_filter, asl_configuration.time_24 = "1" == asl_configuration.time_format, asl_configuration.smooth_pan = "1" == asl_configuration.smooth_pan, asl_configuration.user_center = !!asl_configuration.user_center, asl_configuration.zoom_li = 12, asl_configuration.enter_key = !0, asl_configuration.user_center = !1, "" != asl_configuration.full_height && $("#asl-storelocator").height(jQuery(window).height() + "px");
            var asl_lat = asl_configuration.default_lat ? parseFloat(asl_configuration.default_lat) : 39.9217698526,
                asl_lng = asl_configuration.default_lng ? parseFloat(asl_configuration.default_lng) : -75.5718432,
                categories = {},
                asl_date = new Date;
            asl_configuration.default_lat = asl_lat, asl_configuration.default_lng = asl_lng, asl_configuration.show_opened = !0, $("#asl-dist-unit").html(asl_configuration.distance_unit), asl_engine.helper.asl_leadzero = function(t) {
                return 9 < t ? "" + t : "0" + t
            }, asl_engine.helper.asl_timeConvert = function(t) {
                var e = $.trim(t).toUpperCase();
                if (!/(1[012]|[1-9]):[0-5][0-9][ ]?(AM|PM)/.test(e)) return 0;
                var o = Number(e.match(/^(\d+)/)[1]),
                    i = Number(e.match(/:(\d+)/)[1]),
                    s = -1 != e.indexOf("PM") ? "PM" : "AM";
                return "PM" == s && o < 12 && (o += 12), "AM" == s && 12 == o && (o -= 12), o + i / 100
            }, asl_engine.helper.between = function(t, e, o) {
                return e < t && t < o
            }, asl_engine.helper.implode = function(t, e) {
                for (var o = [], i = 0, s = t.length; i < s; i++) t[i] && o.push(t[i]);
                return o.join(e)
            }, asl_engine.helper.toObject_ = function(t, e) {
                for (var o = {}, i = 0, s = e.length; i < s; i++) o[t[i]] = e[i];
                return o
            }, asl_engine.helper.distanceCalc = function(t) {
                var e = this.getLocation(),
                    o = asl_locator.toRad_(e.lat()),
                    i = asl_locator.toRad_(e.lng()),
                    s = asl_locator.toRad_(t.lat()),
                    a = s - o,
                    n = asl_locator.toRad_(t.lng()) - i,
                    r = Math.sin(a / 2) * Math.sin(a / 2) + Math.cos(o) * Math.cos(s) * Math.sin(n / 2) * Math.sin(n / 2);
                return 6371 * (2 * Math.atan2(Math.sqrt(r), Math.sqrt(1 - r)))
            }, asl_engine.dataSource = function() {
                this.stores_ = [], this.remote_url = WPLSL_REMOTE.ajax_url
            }, asl_engine.dataSource.prototype.sortDistance = function(o, t) {
                t = t.sort(function(t, e) {
                    return t.distanceTo(o) - e.distanceTo(o)
                })
            }, asl_engine.dataSource.prototype.sortBy = function(o, t) {
                t.sort(function(t, e) {
                    return t.props_[o] > e.props_[o] ? 1 : e.props_[o] > t.props_[o] ? -1 : 0
                })
            }, asl_engine.dataSource.prototype.sortByDesc = function(o, t) {
                t.sort(function(t, e) {
                    return t.props_[o] < e.props_[o] ? 1 : e.props_[o] < t.props_[o] ? -1 : 0
                })
            };
            var asl_first_load = !1,
                asl_view = null,
                asl_panel = null,
                stopLoadMore = null,
                xhr_loadmore = null;

            asl_engine.dataSource.prototype.fetch_remote_data = function(i) {
                var s = this;
                $(".asl-p-cont .asl-overlay").show();
                var t = {
                    action: "wplsl_load_stores",
                    category: $('[name="store_category"]').val(),
                    nonce: WPLSL_REMOTE.nonce,
                    paged: $('#wpl-store-list-container').attr('data-paged'),
                    per_page: $('#wpl-store-list-container').attr('data-per_page'),
                    layout: asl_configuration.layout ? 1 : 0
                };
                asl_configuration.stores && (t.stores = asl_configuration.stores), asl_configuration.category && (t.category = asl_configuration.category), $.ajax({
                    url: WPLSL_REMOTE.ajax_url,
                    data: t,
                    type: "GET",
                    dataType: "json",
                    success: function(t) {
                        s.stores_ = s.parseData(t);
                        s.stores_;


                        if (!asl_first_load) {
                            if (asl_first_load = !0, asl_view = new asl_locator.View(map, s, {
                                    geolocation: !1,
                                    features: s.getDSFeatures()
                                }), asl_panel = new asl_locator.Panel(document.getElementById("wpl-seller-panel"), {
                                    view: asl_view
                                }), asl_view._panel = asl_panel, asl_configuration.smooth_pan) {
                                function e() {
                                    asl_view.map_w = $(map.getDiv()).width(), asl_view.map_h = $(map.getDiv()).height()
                                }
                                asl_view._overlay = new google.maps.OverlayView, asl_view._overlay.draw = function() {}, asl_view._overlay.setMap(map), $(window).resize(e), e()
                            }
                            var o = jQuery(".asl-p-cont #asl-geolocation-agile-modal");
                            o.find(".close").bind("click", function(t) {
                                o.removeClass("in"), window.setTimeout(function() {
                                    o.css("display", "none")
                                }, 300)
                            }), "0" != asl_configuration.prompt_location && (o.css("display", "block"), window.setTimeout(function() {
                                o.addClass("in")
                            }, 300), $(".asl-p-cont #asl-btn-geolocation").bind("click", function() {
                                asl_view.geolocate_(), o.removeClass("in").css("display", "none")
                            })), $(".asl-p-cont .icon-direction-outline").bind("click", function(t) {
                                asl_view.geolocate_()
                            }), asl_configuration.user_center && asl_view.measure_distance(new google.maps.LatLng(asl_configuration.default_lat, asl_configuration.default_lng))
                        }

                        asl_view.refreshView(!0), $(".asl-p-cont .asl-overlay").hide(), i && map.panTo(i)
                    },
                    dataType: "json"
                })
            }, asl_engine.dataSource.prototype.scroll_load_store = function(i) {
                var s = this,
                    storeList = X('#wpl-store-list-container');

                storeList.on('scroll', function() {
                    var itemHeight = $(this)[0].scrollHeight - ( $('.store-item').height() * 2 );
                    
                    if( $(this).scrollTop() + $(this).innerHeight() > itemHeight && ! stopLoadMore ) {
                        if( $('#wpl-seller-panel > .store-loading').length <= 0 ) {
                            $('#wpl-seller-panel').append('<div class="store-loading"><button class="btn"><img src="' + wplModules.site_url + 'assets/images/sloader.svg"> Loading...</button></div>');

                            if( xhr_loadmore && xhr_loadmore.readyState != 4 ){ xhr_loadmore.abort(); }
            
                            paged = parseInt( storeList.attr('data-paged') ) + 1,
                            per_page = storeList.attr('data-per_page');
                            xhr_loadmore = jQuery.ajax({
                                url: wplModules.ajax_url,
                                data: {
                                    action: 'woopanel_loadmore_store',
                                    paged: paged,
                                    per_page: per_page
                                },
                                type: 'POST',
                                datatype: 'json',
                                success: function( response ) {
                                    $('.store-loading').remove();
                                    if(response.complete != undefined ) {
                                        
                                        storeList.attr('data-paged', paged);

                                        var current = s.parseData(response.results);
                                        window.asl_data_source.stores_ = window.asl_data_source.stores_.concat(current);

                                        asl_view.refreshView(!0);
                                    }else {
                                        stopLoadMore = true;
                                    }
                                },
                                error:function( xhr, status, error ) {

                                }
                            });

                        }
                    }
                });
            }, asl_engine.dataSource.prototype.load_locator = function() {
                var that = this;
                if (!document.getElementById("asl-map-canv")) return !1;
                var maps_params = {
                    center: new google.maps.LatLng(asl_lat, asl_lng),
                    zoom: parseInt(asl_configuration.zoom),
                    scrollwheel: asl_configuration.scroll_wheel,
                    gestureHandling: "cooperative",
                    mapTypeId: asl_configuration.map_type
                };
                if (asl_configuration.maxZoom && !isNaN(asl_configuration.maxZoom) && (maps_params.maxZoom = parseInt(asl_configuration.maxZoom)), asl_configuration.minZoom && !isNaN(asl_configuration.minZoom) && (maps_params.minZoom = parseInt(asl_configuration.minZoom)), map = new google.maps.Map(document.getElementById("asl-map-canv"), maps_params), asl_configuration.map_layout) {
                    var map_style = eval("(" + asl_configuration.map_layout + ")");
                    map.set("styles", map_style)
                }
                var _features = [];
                for (var i in asl_categories) {
                    var cat = asl_categories[i];
                    that.FEATURES_.add(new asl_locator.Feature(cat.id, cat.name, cat.icon, cat.s))
                }
                that.fetch_remote_data()
                that.scroll_load_store();
            }, asl_engine.dataSource.prototype.FEATURES_ = new asl_locator.FeatureSet, asl_engine.dataSource.prototype.getDSFeatures = function() {
                return this.FEATURES_
            }, asl_engine.dataSource.prototype.parseData = function(t) {
                var e = [],
                    o = asl_date.getHours() + asl_date.getMinutes() / 100,
                    i = asl_date.getDay(),
                    s = asl_categories;
                asl_categories = {};
                var a = Object.keys(s);
                for (var n in a) "object" == typeof s[a[n]] && (asl_categories[String(a[n])] = s[a[n]], asl_categories[a[n]].len = 0);
                i = {
                    1: "mon",
                    2: "tue",
                    3: "wed",
                    4: "thu",
                    5: "fri",
                    6: "sat",
                    0: "sun"
                } [i];
                for (var r = 0; r < t.length; r++) {
                    var l = t[r];
                    l.id = parseInt(l.id), l.ordr = !l.ordr || isNaN(l.ordr) ? 0 : parseInt(l.ordr), l.lat = parseFloat(l.lat), l.lng = parseFloat(l.lng);
                    var c = new google.maps.LatLng(l.lat, l.lng);
                    l.open_hours = l.open_hours ? l.open_hours : null, l.state || (l.state = "");
                    var _ = asl_engine.helper.implode([l.city, l.state, l.postal_code], ", "),
                        d = [l.street, _];
                    l.address = asl_engine.helper.implode(d, " <br> ");
                    var p = l.categories ? l.categories.split(",") : [],
                        h = [],
                        g = [];
                    for (var u in p) {
                        var f = p[u].toString();
                        asl_categories[f] ? (asl_categories[f].len++, h.push(asl_categories[f]), g.push(asl_categories[f].name)) : delete p[u]
                    }
                    if (l.c_names = asl_engine.helper.implode(g, ", "), l.categories = h, l.city = $.trim(l.city), l.country = $.trim(l.country), l.state || (l.state = ""), asl_configuration.additional_info || (l.description_2 = null), l.marker_id = l.marker_id ? l.marker_id.toString() : "", l.open_hours) {
                        l.open = !1, l.open_hours = JSON.parse(l.open_hours);
                        var m = l.open_hours[i];
                        if (l.open_hours = [], "1" == m) l.open = !0;
                        else if ("0" == m) l.open = !1;
                        else if (m)
                            for (var v in m)
                                if (m.hasOwnProperty(v)) {
                                    var y = m[v].split(" - ");
                                    l.start_time = y[0], l.end_time = y[1];
                                    var w = 0 != l.start_time ? asl_engine.helper.asl_timeConvert(l.start_time) : 0,
                                        x = 0 != l.end_time ? asl_engine.helper.asl_timeConvert(l.end_time) : 24;
                                    if (0 == x && (x = 24), l.open || (l.open = !(!l.start_time || !l.end_time) && asl_engine.helper.between(o, w, x)), asl_configuration.time_24) {
                                        w += .01, w = parseFloat(w).toFixed(2);
                                        var b = String(w).split(".");
                                        b[0] = asl_engine.helper.asl_leadzero(parseInt(b[0])), b[1] = asl_engine.helper.asl_leadzero(parseInt(b[1]) - 1), l.start_time = b.join(":"), x += .01, x = parseFloat(x).toFixed(2);
                                        var L = String(x).split(".");
                                        L[0] = asl_engine.helper.asl_leadzero(parseInt(L[0])), L[1] = asl_engine.helper.asl_leadzero(parseInt(L[1]) - 1), l.end_time = L.join(":")
                                    } else {
                                        var k = l.start_time.split(":"),
                                            S = l.end_time.split(":");
                                        k[0] && (k[0] = asl_engine.helper.asl_leadzero(parseInt(k[0]))), l.start_time = k.join(":"), S[0] && (S[0] = asl_engine.helper.asl_leadzero(parseInt(S[0]))), l.end_time = S.join(":")
                                    }
                                    l.open_hours.push(l.start_time + " - " + l.end_time)
                                } l.open_hours = l.open_hours.join(" <br> ")
                    } else l.open = !0;
                    var C = new asl_locator.Store(l.id, c, p, l);
                    e.push(C)
                }
                return e
            };
            var data_source = new asl_engine.dataSource;
            window.asl_data_source = data_source, data_source.getStores = function(t, e, o, i) {
                for (var s, a = [], n = 0; s = this.stores_[n]; n++) s.hasAnyCategory(e) && a.push(s);
                asl_configuration.sort_by ? "ordr" == asl_configuration.sort_by ? this.sortByDesc(asl_configuration.sort_by, a) : this.sortBy(asl_configuration.sort_by, a) : a && asl_view.dest_coords ? this.sortDistance(asl_view.dest_coords, a) : t && asl_configuration.sort_by_bound && this.sortDistance(t.getCenter(), a), o(a)
            }, data_source.load_locator()
        }
    }(asl_jQuery, asl_underscore));

    function InfoBox(t) {
        t = t || {}, google.maps.OverlayView.apply(this, arguments), this.content_ = t.content || "", this.disableAutoPan_ = t.disableAutoPan || !1, this.maxWidth_ = t.maxWidth || 0, this.pixelOffset_ = t.pixelOffset || new google.maps.Size(0, 0), this.position_ = t.position || new google.maps.LatLng(0, 0), this.zIndex_ = t.zIndex || null, this.boxClass_ = t.boxClass || "infoBox", this.boxStyle_ = t.boxStyle || {}, this.closeBoxMargin_ = t.closeBoxMargin || "2px", this.closeBoxURL_ = t.closeBoxURL || "http://www.google.com/intl/en_us/mapfiles/close.gif", "" === t.closeBoxURL && (this.closeBoxURL_ = ""), this.infoBoxClearance_ = t.infoBoxClearance || new google.maps.Size(1, 1), void 0 === t.visible && (void 0 === t.isHidden ? t.visible = !0 : t.visible = !t.isHidden), this.isHidden_ = !t.visible, this.alignBottom_ = t.alignBottom || !1, this.pane_ = t.pane || "floatPane", this.enableEventPropagation_ = t.enableEventPropagation || !1, this.div_ = null, this.closeListener_ = null, this.moveListener_ = null, this.contextListener_ = null, this.eventListeners_ = null, this.fixedWidthSet_ = null
    }

    function _isMobileDevice() {
        var t, e = !1;
        return t = navigator.userAgent || navigator.vendor || window.opera, (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(t) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(t.substr(0, 4))) && (e = !0), e
    }
});