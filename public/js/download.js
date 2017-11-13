/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 11);
/******/ })
/************************************************************************/
/******/ ({

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(12);


/***/ }),

/***/ 12:
/***/ (function(module, exports) {

//download.js v3.0, by dandavis; 2008-2014. [CCBY2] see http://danml.com/download.html for tests/usage
// v1 landed a FF+Chrome compat way of downloading strings to local un-named files, upgraded to use a hidden frame and optional mime
// v2 added named files via a[download], msSaveBlob, IE (10+) support, and window.URL support for larger+faster saves than dataURLs
// v3 added dataURL and Blob Input, bind-toggle arity, and legacy dataURL fallback was improved with force-download mime and base64 support

// data can be a string, Blob, File, or dataURL


function download(data, strFileName, strMimeType) {

	var self = window,
	    // this script is only for browsers anyway...
	u = "application/octet-stream",
	    // this default mime also triggers iframe downloads
	m = strMimeType || u,
	    x = data,
	    D = document,
	    a = D.createElement("a"),
	    z = function z(a) {
		return String(a);
	},
	    B = self.Blob || self.MozBlob || self.WebKitBlob || z,
	    BB = self.MSBlobBuilder || self.WebKitBlobBuilder || self.BlobBuilder,
	    fn = strFileName || "download",
	    blob,
	    b,
	    ua,
	    fr;

	//if(typeof B.bind === 'function' ){ B=B.bind(self); }

	if (String(this) === "true") {
		//reverse arguments, allowing download.bind(true, "text/xml", "export.xml") to act as a callback
		x = [x, m];
		m = x[0];
		x = x[1];
	}

	//go ahead and download dataURLs right away
	if (String(x).match(/^data\:[\w+\-]+\/[\w+\-]+[,;]/)) {
		return navigator.msSaveBlob ? // IE10 can't do a[download], only Blobs:
		navigator.msSaveBlob(d2b(x), fn) : saver(x); // everyone else can save dataURLs un-processed
	} //end if dataURL passed?

	try {

		blob = x instanceof B ? x : new B([x], { type: m });
	} catch (y) {
		if (BB) {
			b = new BB();
			b.append([x]);
			blob = b.getBlob(m); // the blob
		}
	}

	function d2b(u) {
		var p = u.split(/[:;,]/),
		    t = p[1],
		    dec = p[2] == "base64" ? atob : decodeURIComponent,
		    bin = dec(p.pop()),
		    mx = bin.length,
		    i = 0,
		    uia = new Uint8Array(mx);

		for (i; i < mx; ++i) {
			uia[i] = bin.charCodeAt(i);
		}return new B([uia], { type: t });
	}

	function saver(url, winMode) {

		if ('download' in a) {
			//html5 A[download] 			
			a.href = url;
			a.setAttribute("download", fn);
			a.innerHTML = "downloading...";
			D.body.appendChild(a);
			setTimeout(function () {
				a.click();
				D.body.removeChild(a);
				if (winMode === true) {
					setTimeout(function () {
						self.URL.revokeObjectURL(a.href);
					}, 250);
				}
			}, 66);
			return true;
		}

		//do iframe dataURL download (old ch+FF):
		var f = D.createElement("iframe");
		D.body.appendChild(f);
		if (!winMode) {
			// force a mime that will download:
			url = "data:" + url.replace(/^data:([\w\/\-\+]+)/, u);
		}

		f.src = url;
		setTimeout(function () {
			D.body.removeChild(f);
		}, 333);
	} //end saver 


	if (navigator.msSaveBlob) {
		// IE10+ : (has Blob, but not a[download] or URL)
		return navigator.msSaveBlob(blob, fn);
	}

	if (self.URL) {
		// simple fast and modern way using Blob and URL:
		saver(self.URL.createObjectURL(blob), true);
	} else {
		// handle non-Blob()+non-URL browsers:
		if (typeof blob === "string" || blob.constructor === z) {
			try {
				return saver("data:" + m + ";base64," + self.btoa(blob));
			} catch (y) {
				return saver("data:" + m + "," + encodeURIComponent(blob));
			}
		}

		// Blob but not URL:
		fr = new FileReader();
		fr.onload = function (e) {
			saver(this.result);
		};
		fr.readAsDataURL(blob);
	}
	return true;
} /* end download() */

/***/ })

/******/ });