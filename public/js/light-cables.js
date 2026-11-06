/**
 * GEZUX Light Cables — Glowing Fiber-Optic Energy Stream Effect (Vanilla JS)
 */
(function() {
    const MAX_DPR = 1.0;
    const MAX_CABLES = 18;
    const GRAIN = 0.012;

    const VERT_SRC = `
    attribute vec2 a_pos;
    void main() { gl_Position = vec4(a_pos, 0.0, 1.0); }
    `;

    const FRAG_SRC = `
    #ifdef GL_FRAGMENT_PRECISION_HIGH
    precision highp float;
    #else
    precision mediump float;
    #endif

    uniform vec2  uRes;
    uniform float uTime;
    uniform vec2  uMouse;
    uniform float uHover;
    uniform vec3  uBg;
    uniform vec3  uBase;
    uniform vec3  uAccent;
    uniform vec3  uHigh;
    uniform float uCount;
    uniform float uBend;
    uniform float uSpread;
    uniform float uWStart;
    uniform float uWEnd;
    uniform float uAxis;
    uniform float uDir;
    uniform float uPosX;
    uniform float uPosY;
    uniform float uThick;
    uniform float uFlow;
    uniform float uPulses;
    uniform float uGrab;
    uniform float uGrain;

    float sat(float x) { return clamp(x, 0.0, 1.0); }

    float h21(vec2 p) {
        p = fract(p * vec2(123.34, 456.21));
        p += dot(p, p + 34.56);
        return fract(p.x * p.y);
    }

    float vnoise(vec2 p) {
        vec2 i = floor(p), f = fract(p);
        f = f * f * (3.0 - 2.0 * f);
        float a = h21(i), b = h21(i + vec2(1.0, 0.0));
        float c = h21(i + vec2(0.0, 1.0)), d = h21(i + vec2(1.0, 1.0));
        return mix(mix(a, b, f.x), mix(c, d, f.x), f.y);
    }

    float fbm3(vec2 p) {
        float s = 0.0, a = 0.5;
        for (int i = 0; i < 3; i++) { s += a * vnoise(p); p = p * 2.07 + vec2(4.1, 2.3); a *= 0.5; }
        return s;
    }

    void main() {
        float ar = uRes.x / max(uRes.y, 1.0);
        vec2 uv = gl_FragCoord.xy / uRes;
        vec2 p = (uv - 0.5) * vec2(ar, 1.0);
        float t = uTime;

        vec2 pAdj = p - vec2(uPosX * ar, uPosY) * 0.5;

        vec3 col = uBg;
        vec2 sm = p - vec2(-0.05, -0.28);
        col += uBase * exp(-pow(length(sm * vec2(0.75, 1.9)) / 0.42, 1.7)) * 0.50
             * (0.6 + 0.6 * fbm3(p * 2.4 + vec2(t * 0.05, 0.0)));

        float extent = mix(ar, 1.0, uAxis);
        float along = mix(pAdj.x, pAdj.y, uAxis) * uDir;
        float across = mix(pAdj.y, pAdj.x, uAxis);

        float s01 = sat((along + extent * 0.5) / max(extent, 0.001));
        vec2 ptrRaw = (uMouse - 0.5) * vec2(ar, 1.0);
        float ptrAlong = mix(ptrRaw.x, ptrRaw.y, uAxis) * uDir;
        float ptrAcross = mix(ptrRaw.y, ptrRaw.x, uAxis);

        float kAlong = -0.18;
        float kAcross = 0.10;
        vec3 acc = vec3(0.0);
        float surge = 0.0;

        for (int i = 0; i < 48; i++) {
            if (float(i) >= uCount) break;
            float fi = float(i) / max(uCount - 1.0, 1.0);
            float o = fi - 0.5;
            float rnd = h21(vec2(fi * 7.31, 2.0));

            float kAlongi = kAlong + o * 0.10 + (rnd - 0.5) * 0.03;
            float bi = uBend * (0.88 + 0.24 * rnd);
            float spread = uSpread * mix(uWStart, uWEnd, s01) * (0.090 + 0.34 * sat((along - kAlong) * 0.85 + 0.25));
            float bendAlong = sqrt((along - kAlongi) * (along - kAlongi) + 0.0035);
            float yy = kAcross + o * spread - bi * bendAlong + 0.008 * sin(along * 3.0 + fi * 19.0);

            float gx = exp(-pow((along - ptrAlong) / max(uGrab * 0.30, 0.02), 2.0));
            yy = mix(yy, ptrAcross + o * spread * 0.55, gx * 0.60 * uHover);

            float dd = (across - yy) / (uThick * 0.0038);
            float core = 1.0 / (1.0 + dd * dd * 9.0);
            float sheath = 1.0 / (1.0 + dd * dd * 0.6);

            float ph = s01 * uPulses - t * uFlow * 0.55 - rnd * 0.22;
            float f = fract(ph);
            float pulse = exp(-pow((f - 0.55) / 0.15, 2.0));
            float w = (0.22 + 1.60 * pulse) * (0.55 + 0.45 * rnd);

            acc += (uHigh * core * 1.45 + uAccent * sheath * 0.20) * w;
            surge += core * gx;
        }

        float feed = smoothstep(0.0, 0.09, s01) * smoothstep(1.02, 0.30, s01);
        col += acc * feed * 0.20;
        col += uHigh * surge * 0.05 * uHover;
        col += (h21(gl_FragCoord.xy + fract(uTime) * 71.0) - 0.5) * uGrain;

        gl_FragColor = vec4(max(col, 0.0), 1.0);
    }
    `;

    function parseColor(input, fb) {
        if (!input) return fb;
        const str = String(input).trim();
        if (str.charAt(0) === "#") {
            let hex = str.slice(1);
            if (hex.length === 3 || hex.length === 4) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }
            if (hex.length >= 6) {
                const r = parseInt(hex.slice(0, 2), 16);
                const g = parseInt(hex.slice(2, 4), 16);
                const b = parseInt(hex.slice(4, 6), 16);
                if (!isNaN(r) && !isNaN(g) && !isNaN(b)) return [r / 255, g / 255, b / 255];
            }
            return fb;
        }
        return fb;
    }

    function clampN(v, lo, hi) {
        return v < lo ? lo : v > hi ? hi : v;
    }

    function compile(gl, type, src) {
        const sh = gl.createShader(type);
        if (!sh) return null;
        gl.shaderSource(sh, src);
        gl.compileShader(sh);
        if (!gl.getShaderParameter(sh, gl.COMPILE_STATUS)) {
            gl.deleteShader(sh);
            return null;
        }
        return sh;
    }

    window.initLightCables = function(canvasId, config) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const cfg = Object.assign({
            background: "#070708",
            baseColor: "#0B0B10",
            accentColor: "#7C3AED",
            highlight: "#C084FC",
            speed: 38,
            hover: 130,
            grab: 120,
            direction: "ttb",
            positionX: 0,
            positionY: 0,
            bundle: { count: 42, bend: 38, spread: 95, thickness: 130, widthStart: 10, widthEnd: 260 },
            flow: { flow: 220, pulses: 2 }
        }, config || {});

        const gl = canvas.getContext("webgl", { antialias: false, alpha: false, depth: false });
        if (!gl) return;

        const vs = compile(gl, gl.VERTEX_SHADER, VERT_SRC);
        const fs = compile(gl, gl.FRAGMENT_SHADER, FRAG_SRC);
        if (!vs || !fs) return;
        const prog = gl.createProgram();
        gl.attachShader(prog, vs);
        gl.attachShader(prog, fs);
        gl.linkProgram(prog);
        if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) return;
        gl.useProgram(prog);

        const buf = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, buf);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
        const posLoc = gl.getAttribLocation(prog, "a_pos");
        gl.enableVertexAttribArray(posLoc);
        gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0);

        const locs = {};
        const u = (name) => {
            if (!(name in locs)) locs[name] = gl.getUniformLocation(prog, name);
            return locs[name];
        };

        const v = {
            bg: cfg.background,
            base: cfg.baseColor,
            accent: cfg.accentColor,
            high: cfg.highlight,
            speed: clampN(cfg.speed || 50, 0, 100) / 50,
            hover: clampN(cfg.hover || 100, 0, 200) / 100,
            count: Math.round(clampN(cfg.bundle.count || 36, 4, MAX_CABLES)),
            bend: clampN(cfg.bundle.bend || 35, 0, 150) / 100,
            spread: clampN(cfg.bundle.spread || 90, 10, 300) / 100,
            thickness: clampN(cfg.bundle.thickness || 110, 20, 400) / 100,
            wStart: clampN(cfg.bundle.widthStart || 20, 0, 300) / 100,
            wEnd: clampN(cfg.bundle.widthEnd || 220, 0, 300) / 100,
            flow: clampN(cfg.flow.flow || 180, 0, 300) / 100,
            pulses: clampN(cfg.flow.pulses || 2, 1, 12),
            grab: clampN(cfg.grab || 100, 10, 300) / 100,
            axis: cfg.direction === "ttb" || cfg.direction === "btt" ? 1 : 0,
            dir: cfg.direction === "rtl" || cfg.direction === "ttb" ? -1 : 1,
            posX: clampN(cfg.positionX || 0, -100, 100) / 100,
            posY: clampN(cfg.positionY || 0, -100, 100) / 100,
        };

        const ptr = { x: 0.5, y: 0.5, tx: 0.5, ty: 0.5, on: 0, onTarget: 0 };
        let last = performance.now();
        let clock = 0;

        const resize = () => {
            const dpr = Math.min(window.devicePixelRatio || 1, MAX_DPR);
            const cw = canvas.parentElement ? canvas.parentElement.clientWidth : window.innerWidth;
            const ch = canvas.parentElement ? canvas.parentElement.clientHeight : window.innerHeight;
            canvas.width = Math.max(1, Math.round(cw * dpr));
            canvas.height = Math.max(1, Math.round(ch * dpr));
        };
        resize();
        window.addEventListener("resize", resize);

        window.addEventListener("pointermove", (e) => {
            const r = canvas.getBoundingClientRect();
            if (r.width <= 0 || r.height <= 0) return;
            ptr.tx = clampN((e.clientX - r.left) / r.width, 0, 1);
            ptr.ty = clampN((e.clientY - r.top) / r.height, 0, 1);
            ptr.onTarget = 1;
        });

        let isCablesVisible = true;
        let cablesAnimId = null;

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    isCablesVisible = e.isIntersecting;
                    if (isCablesVisible && !cablesAnimId) {
                        last = performance.now();
                        cablesAnimId = requestAnimationFrame(render);
                    } else if (!isCablesVisible && cablesAnimId) {
                        cancelAnimationFrame(cablesAnimId);
                        cablesAnimId = null;
                    }
                });
            }, { threshold: 0.05 });
            obs.observe(canvas);
        }

        function render(now) {
            if (!isCablesVisible) {
                cablesAnimId = null;
                return;
            }
            cablesAnimId = requestAnimationFrame(render);
            const dt = Math.min(0.05, (now - last) / 1000);
            last = now;

            clock = (clock + dt * v.speed) % 3600;

            const k = 1 - Math.exp(-6 * dt);
            ptr.on += (ptr.onTarget - ptr.on) * k;
            ptr.x += ((ptr.onTarget > 0 ? ptr.tx : 0.5) - ptr.x) * k;
            ptr.y += ((ptr.onTarget > 0 ? ptr.ty : 0.5) - ptr.y) * k;

            gl.viewport(0, 0, canvas.width, canvas.height);

            gl.uniform2f(u("uRes"), canvas.width, canvas.height);
            gl.uniform1f(u("uTime"), clock);
            gl.uniform2f(u("uMouse"), ptr.x, 1 - ptr.y);
            gl.uniform1f(u("uHover"), Math.min(1, ptr.on) * v.hover);

            const cg = parseColor(v.bg, [0.027, 0.027, 0.031]);
            const cb = parseColor(v.base, [0.07, 0.07, 0.1]);
            const ca = parseColor(v.accent, [0.486, 0.227, 0.929]);
            const ch2 = parseColor(v.high, [0.659, 0.333, 0.968]);

            gl.uniform3f(u("uBg"), cg[0], cg[1], cg[2]);
            gl.uniform3f(u("uBase"), cb[0], cb[1], cb[2]);
            gl.uniform3f(u("uAccent"), ca[0], ca[1], ca[2]);
            gl.uniform3f(u("uHigh"), ch2[0], ch2[1], ch2[2]);
            gl.uniform1f(u("uCount"), v.count);
            gl.uniform1f(u("uBend"), v.bend);
            gl.uniform1f(u("uSpread"), v.spread);
            gl.uniform1f(u("uWStart"), v.wStart);
            gl.uniform1f(u("uWEnd"), v.wEnd);
            gl.uniform1f(u("uAxis"), v.axis);
            gl.uniform1f(u("uDir"), v.dir);
            gl.uniform1f(u("uPosX"), v.posX);
            gl.uniform1f(u("uPosY"), v.posY);
            gl.uniform1f(u("uThick"), v.thickness);
            gl.uniform1f(u("uFlow"), v.flow);
            gl.uniform1f(u("uPulses"), v.pulses);
            gl.uniform1f(u("uGrab"), v.grab);
            gl.uniform1f(u("uGrain"), GRAIN);
            gl.drawArrays(gl.TRIANGLES, 0, 3);
        }
        requestAnimationFrame(render);
    };
})();
