/**
 * GEZUX Master Animations Engine (Pure Vanilla JS / Canvas2D / WebGL / Three.js)
 * 60fps lightweight, hardware-accelerated, responsive, Light/Dark adaptive
 * Includes:
 * 1. Hero: Floating Violet/Purple Flakes + Interactive Magnetic Cursor Ring Field
 * 2. Starfield Buttons: Twinkling Pixel Starfield + Orbiting Perimeter Light Beams
 * 3. System Architecture: Flowing Fiber-Optic Light Cables
 * 4. Campaign Marketplace: 3D Undulating Particle Wave Mesh (Three.js)
 * 5. Trust & Verification: Radiant Telemetry Data Strings & Flowing Pulses
 */
(function() {
    'use strict';

    function isDark() {
        return document.documentElement.classList.contains('dark');
    }

    // =========================================================================
    // 1. HERO: FLOATING VIOLET/BLUE DOTS + MAGNETIC CURSOR RING FIELD
    // =========================================================================
    function initHeroParticleField() {
        const canvas = document.getElementById('cursor-ring-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let w = 1, h = 1;
        const DPR = Math.min(window.devicePixelRatio || 1, 1.5);

        function resize() {
            const p = canvas.parentElement || document.body;
            const parentW = p.clientWidth || canvas.offsetWidth || window.innerWidth;
            const parentH = p.clientHeight || canvas.offsetHeight || 800;
            w = canvas.width = Math.max(300, Math.round(parentW * DPR));
            h = canvas.height = Math.max(300, Math.round(parentH * DPR));
            canvas.style.width = '100%';
            canvas.style.height = '100%';
        }
        resize();
        window.addEventListener('resize', resize);
        setTimeout(resize, 150);
        setTimeout(resize, 800);

        // Particle configuration: dense field of glowing circular DOTS
        const isMobile = window.innerWidth < 768;
        const PARTICLE_COUNT = isMobile ? 200 : 480;

        const LIGHT_COLORS = [
            '#7C3AED', '#8B5CF6', '#A855F7', '#6366F1', '#3B82F6', '#9333EA', '#4F46E5', '#60A5FA'
        ];
        const DARK_COLORS = [
            '#C084FC', '#A855F7', '#818CF8', '#38BDF8', '#DDD6FE', '#A78BFA', '#8B5CF6', '#60A5FA'
        ];

        const particles = [];
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            // Varied dot radii: 70% micro-dots, 25% medium dots, 5% prominent dots
            const tier = Math.random();
            let baseRadius;
            if (tier < 0.70) {
                baseRadius = (1.4 + Math.random() * 1.2) * DPR;
            } else if (tier < 0.95) {
                baseRadius = (2.6 + Math.random() * 1.4) * DPR;
            } else {
                baseRadius = (4.0 + Math.random() * 1.5) * DPR;
            }

            particles.push({
                x: Math.random() * (w || 1400),
                y: Math.random() * (h || 900),
                vx: (Math.random() - 0.5) * 0.35 * DPR,
                vy: -(0.25 + Math.random() * 0.5) * DPR, // soft upward drift
                baseRadius: baseRadius,
                currentRadius: baseRadius,
                colorIdx: Math.floor(Math.random() * LIGHT_COLORS.length),
                alpha: 0.45 + Math.random() * 0.45,
                wobbleSpeed: 0.6 + Math.random() * 1.4,
                wobblePhase: Math.random() * Math.PI * 2,
                repelX: 0,
                repelY: 0,
                energy: 0
            });
        }

        const pointer = {
            x: -1000,
            y: -1000,
            targetX: -1000,
            targetY: -1000,
            active: false
        };

        window.addEventListener('pointermove', (e) => {
            const r = canvas.getBoundingClientRect();
            pointer.targetX = (e.clientX - r.left) * DPR;
            pointer.targetY = (e.clientY - r.top) * DPR;
            pointer.active = true;
        });

        window.addEventListener('pointerleave', () => {
            pointer.active = false;
        });

        let isVisible = true;
        let animId = null;
        let time = 0;
        let wanderAngle = 0;

        function isHeroVisible() {
            return (window.scrollY < (window.innerHeight * 1.8)) || isVisible;
        }

        if ('IntersectionObserver' in window) {
            try {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach((e) => {
                        isVisible = e.isIntersecting || (window.scrollY < window.innerHeight);
                        if (isHeroVisible() && !animId) frame();
                    });
                }, { threshold: 0 });
                obs.observe(canvas.closest('section') || canvas.parentElement || canvas);
            } catch (e) {}
        }

        // Immediately start animation loop
        frame();

        function frame() {
            if (!isHeroVisible()) {
                animId = null;
                return;
            }
            animId = requestAnimationFrame(frame);

            time += 0.016;
            wanderAngle += 0.012;
            ctx.clearRect(0, 0, w, h);

            // Autonomous wander if pointer is inactive
            let ringCenterX, ringCenterY;
            if (pointer.active) {
                pointer.x += (pointer.targetX - pointer.x) * 0.12;
                pointer.y += (pointer.targetY - pointer.y) * 0.12;
                ringCenterX = pointer.x;
                ringCenterY = pointer.y;
            } else {
                // Gentle wander around the center of hero
                ringCenterX = (w / 2) + Math.sin(wanderAngle * 0.7) * (w * 0.22);
                ringCenterY = (h / 2) + Math.cos(wanderAngle * 0.5) * (h * 0.18);
            }

            const dark = isDark();
            const colors = dark ? DARK_COLORS : LIGHT_COLORS;
            const RING_RADIUS = (isMobile ? 110 : 160) * DPR;
            const RING_THICKNESS = (isMobile ? 35 : 55) * DPR;

            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];

                // Natural upward drift & lateral harmonic oscillation
                p.x += p.vx + Math.sin(time * p.wobbleSpeed + p.wobblePhase) * (0.3 * DPR);
                p.y += p.vy;

                // Wrap around edges
                if (p.y < -20 * DPR) {
                    p.y = h + 15 * DPR;
                    p.x = Math.random() * w;
                }
                if (p.x < -20 * DPR) p.x = w + 15 * DPR;
                if (p.x > w + 20 * DPR) p.x = -15 * DPR;

                // Interactive Cursor Ring Magnetism & Shockwave
                const dx = p.x - ringCenterX;
                const dy = p.y - ringCenterY;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < RING_RADIUS + RING_THICKNESS && dist > 1) {
                    const ringDist = Math.abs(dist - RING_RADIUS);
                    const pushFactor = Math.max(0, 1.0 - (ringDist / RING_THICKNESS));
                    const pushStrength = Math.pow(pushFactor, 1.5) * (pointer.active ? 24 : 10) * DPR;
                    const nx = dx / dist;
                    const ny = dy / dist;

                    p.repelX += nx * pushStrength;
                    p.repelY += ny * pushStrength;
                    p.energy = Math.max(p.energy, pushFactor);
                }

                // Decay spring & energy
                p.repelX *= 0.88;
                p.repelY *= 0.88;
                p.energy *= 0.92;

                const drawX = p.x + p.repelX;
                const drawY = p.y + p.repelY;

                // Dynamic dot scale & alpha boost on excitation
                const targetRadius = p.baseRadius * (1.0 + p.energy * 0.85);
                p.currentRadius += (targetRadius - p.currentRadius) * 0.2;

                const col = colors[p.colorIdx % colors.length];
                const finalAlpha = Math.min(1.0, (p.alpha + p.energy * 0.45) * (dark ? 0.9 : 0.95));

                // Optional halo glow on high energy
                if (p.energy > 0.3) {
                    ctx.beginPath();
                    ctx.arc(drawX, drawY, p.currentRadius * 2.2, 0, Math.PI * 2);
                    ctx.fillStyle = col;
                    ctx.globalAlpha = p.energy * 0.25;
                    ctx.fill();
                }

                // Render crisp CIRCULAR DOT
                ctx.beginPath();
                ctx.arc(drawX, drawY, p.currentRadius, 0, Math.PI * 2);
                ctx.fillStyle = col;
                ctx.globalAlpha = finalAlpha;
                ctx.fill();
            }
        }

        frame();
    }

    // =========================================================================
    // 2. STARFIELD BUTTONS: TWINKLING MATRIX + ORBITING PERIMETER LIGHT BEAMS
    // Matching StarfieldButton.tsx 1:1 with BAND_MASK & Orbiting Light Beams
    // =========================================================================
    function initStarfieldButtons() {
        const buttons = document.querySelectorAll('.starfield-btn');
        if (!buttons.length) return;

        // Point on rounded rectangle perimeter math (from StarfieldButton.tsx)
        function pointOnRoundRect(t, w, h, r) {
            const rr = Math.max(0, Math.min(r, w / 2, h / 2));
            const sx = Math.max(0, w - 2 * rr);
            const sy = Math.max(0, h - 2 * rr);
            const arc = (Math.PI / 2) * rr;
            const total = 2 * sx + 2 * sy + 4 * arc;
            if (total <= 0) return { x: w / 2, y: h / 2 };

            let d = (((t % 1) + 1) % 1) * total + sx / 2;
            d %= total;

            if (d < sx) return { x: rr + d, y: 0 };
            d -= sx;
            if (d < arc) {
                const a = d / rr;
                return { x: w - rr + rr * Math.sin(a), y: rr - rr * Math.cos(a) };
            }
            d -= arc;
            if (d < sy) return { x: w, y: rr + d };
            d -= sy;
            if (d < arc) {
                const a = d / rr;
                return { x: w - rr + rr * Math.cos(a), y: h - rr + rr * Math.sin(a) };
            }
            d -= arc;
            if (d < sx) return { x: w - rr - d, y: h };
            d -= sx;
            if (d < arc) {
                const a = d / rr;
                return { x: rr - rr * Math.sin(a), y: h - rr + rr * Math.cos(a) };
            }
            d -= arc;
            if (d < sy) return { x: 0, y: h - rr - d };
            d -= sy;
            const a = d / rr;
            return { x: rr - rr * Math.cos(a), y: rr - rr * Math.sin(a) };
        }

        const BAND_MASK_CSS = `
            mask-image: linear-gradient(#000 0 0), linear-gradient(#000 0 0);
            mask-clip: border-box, content-box;
            mask-composite: exclude;
            -webkit-mask-image: linear-gradient(#000 0 0), linear-gradient(#000 0 0);
            -webkit-mask-clip: border-box, content-box;
            -webkit-mask-composite: xor;
        `;

        buttons.forEach((btn) => {
            if (btn.dataset.starfieldInit === 'true') return;
            btn.dataset.starfieldInit = 'true';

            const isPrimary = btn.classList.contains('starfield-primary') || !btn.classList.contains('starfield-secondary');
            const glowColor = isPrimary ? '#8B5CF6' : '#94A3B8';
            const accentColor = isPrimary ? '#DDD6FE' : '#F1F5F9';
            const fillColor = isPrimary ? '#0D0C18' : '#121218';

            // Button base styling
            btn.style.boxSizing = 'border-box';
            btn.style.position = 'relative';
            btn.style.border = 'none';
            btn.style.background = 'transparent';
            btn.style.cursor = 'pointer';
            btn.style.userSelect = 'none';
            btn.style.overflow = 'visible';
            btn.style.borderRadius = '9999px';
            btn.style.transition = 'transform 0.2s cubic-bezier(0.16, 1, 0.3, 1)';

            // Track Border Div
            const trackDiv = document.createElement('div');
            trackDiv.setAttribute('aria-hidden', 'true');
            trackDiv.style.cssText = `
                position: absolute;
                inset: 0;
                box-sizing: border-box;
                border-radius: inherit;
                pointer-events: none;
                z-index: 0;
                border: 1px solid ${isDark() ? 'rgba(255,255,255,0.18)' : 'rgba(0,0,0,0.16)'};
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
            `;
            btn.appendChild(trackDiv);

            // Orbiting Light Beams Container
            const bandDiv = document.createElement('div');
            bandDiv.setAttribute('aria-hidden', 'true');
            bandDiv.style.cssText = `
                position: absolute;
                inset: -1px;
                box-sizing: border-box;
                border-radius: inherit;
                pointer-events: none;
                z-index: 1;
                overflow: hidden;
            `;
            btn.appendChild(bandDiv);

            // 2 Orbiting perimeter light orbs (80px radial gradients)
            const lightCount = 2;
            const lightPx = 90;
            const lights = [];
            for (let i = 0; i < lightCount; i++) {
                const orb = document.createElement('div');
                orb.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: ${lightPx}px;
                    height: ${lightPx}px;
                    margin-top: -${lightPx / 2}px;
                    margin-left: -${lightPx / 2}px;
                    pointer-events: none;
                    border-radius: 50%;
                    background: radial-gradient(circle, ${glowColor} 0%, ${glowColor} 45%, rgba(139, 92, 246, 0) 80%);
                    opacity: 0.75;
                    transition: opacity 0.25s ease;
                `;
                bandDiv.appendChild(orb);
                lights.push(orb);
            }

            // Face Container: Move existing children inside it
            const existingNodes = Array.from(btn.childNodes).filter(n => n !== trackDiv && n !== bandDiv);
            const faceSpan = document.createElement('span');
            faceSpan.className = 'relative z-10 box-border w-full h-full flex items-center justify-center gap-2 font-display';
            faceSpan.style.cssText = `
                position: relative;
                z-index: 2;
                box-sizing: border-box;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 13px 28px;
                white-space: nowrap;
                background: ${fillColor};
                color: #F7F7F5;
                border-radius: inherit;
                overflow: hidden;
            `;

            // Canvas Starfield inside Face
            const canvas = document.createElement('canvas');
            canvas.setAttribute('aria-hidden', 'true');
            canvas.style.cssText = `
                position: absolute;
                inset: 0;
                z-index: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                border-radius: inherit;
            `;
            faceSpan.appendChild(canvas);

            // Inner Edge Blur Glow inside Face
            const innerGlow = document.createElement('span');
            innerGlow.setAttribute('aria-hidden', 'true');
            innerGlow.style.cssText = `
                position: absolute;
                inset: 0;
                z-index: 1;
                border: 1.5px solid ${glowColor};
                filter: blur(5px);
                opacity: 0.25;
                border-radius: inherit;
                pointer-events: none;
            `;
            faceSpan.appendChild(innerGlow);

            // Re-append existing content (text & icons) inside faceSpan
            existingNodes.forEach(node => {
                if (node.nodeType === 1) {
                    node.style.position = 'relative';
                    node.style.zIndex = '10';
                }
                faceSpan.appendChild(node);
            });
            btn.appendChild(faceSpan);

            // Setup canvas drawing & matrix cells
            const ctx = canvas.getContext('2d');
            let bw = 1, bh = 1, bdpr = 1;
            let cells = [];

            function buildCells() {
                const step = 4;
                const cols = Math.max(1, Math.floor(bw / step));
                const rows = Math.max(1, Math.floor(bh / step));
                cells = [];
                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        const i = r * cols + c;
                        const lit = Math.random() < 0.58;
                        cells.push({
                            cx: c * step + step / 2,
                            cy: r * step + step / 2,
                            base: lit ? 0.45 + Math.random() * 0.55 : 0.08 + Math.random() * 0.15,
                            speed: 0.8 + Math.random() * 2.2,
                            phase: Math.random() * Math.PI * 2
                        });
                    }
                }
            }

            function resizeBtn() {
                bw = faceSpan.clientWidth || btn.clientWidth || 180;
                bh = faceSpan.clientHeight || btn.clientHeight || 48;
                bdpr = Math.min(window.devicePixelRatio || 1, 2);
                canvas.width = Math.round(bw * bdpr);
                canvas.height = Math.round(bh * bdpr);
                buildCells();
            }
            resizeBtn();
            window.addEventListener('resize', resizeBtn);
            setTimeout(resizeBtn, 150);
            setTimeout(resizeBtn, 600);

            let isHovered = false;
            let isPressed = false;
            let currentReveal = 0.55;

            btn.addEventListener('pointerenter', () => {
                isHovered = true;
                btn.style.transform = isPressed ? 'scale(0.97)' : 'scale(1.025)';
                trackDiv.style.borderColor = glowColor;
                trackDiv.style.boxShadow = `0 0 16px ${glowColor}55`;
            });
            btn.addEventListener('pointerleave', () => {
                isHovered = false;
                isPressed = false;
                btn.style.transform = 'scale(1)';
                trackDiv.style.borderColor = isDark() ? 'rgba(255,255,255,0.18)' : 'rgba(0,0,0,0.16)';
                trackDiv.style.boxShadow = 'none';
            });
            btn.addEventListener('pointerdown', () => {
                isPressed = true;
                btn.style.transform = 'scale(0.97)';
            });
            btn.addEventListener('pointerup', () => {
                isPressed = false;
                btn.style.transform = isHovered ? 'scale(1.025)' : 'scale(1)';
            });

            function btnLoop() {
                requestAnimationFrame(btnLoop);

                const targetReveal = isHovered ? 1.0 : 0.55;
                currentReveal += (targetReveal - currentReveal) * 0.14;

                innerGlow.style.opacity = String(currentReveal * 0.85);

                ctx.setTransform(bdpr, 0, 0, bdpr, 0, 0);
                ctx.clearRect(0, 0, bw, bh);

                const t = performance.now() / 1000;

                // Draw internal starfield
                ctx.globalCompositeOperation = 'lighter';
                ctx.fillStyle = accentColor;
                const dot = 2.4;
                const off = dot / 2;

                for (let k = 0; k < cells.length; k++) {
                    const c = cells[k];
                    const tw = 0.45 + 0.55 * Math.sin(t * c.speed + c.phase);
                    const a = c.base * tw * currentReveal;
                    if (a <= 0.003) continue;
                    ctx.globalAlpha = Math.min(1, a);
                    ctx.fillRect(c.cx - off, c.cy - off, dot, dot);
                }
                ctx.globalAlpha = 1;
                ctx.globalCompositeOperation = 'source-over';

                // Orbiting border light beams (tracing rounded rectangle)
                const elapsedSec = t * 0.28;
                const radius = bh / 2;
                for (let i = 0; i < lights.length; i++) {
                    const orb = lights[i];
                    const pt = pointOnRoundRect(elapsedSec + i / lights.length, bw, bh, radius);
                    orb.style.transform = `translate3d(${pt.x}px, ${pt.y}px, 0)`;
                    orb.style.opacity = String(currentReveal);
                }
            }

            btnLoop();
        });
    }


    // =========================================================================
    // 3. SYSTEM ARCHITECTURE: LIGHT CABLES
    // =========================================================================
    function initArchitectureLightCables() {
        const canvas = document.getElementById('light-cables-canvas');
        if (!canvas) return;

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
            uniform vec3  uBase;
            uniform vec3  uAccent;
            uniform vec3  uHigh;
            uniform float uIsDark;

            float sat(float x) { return clamp(x, 0.0, 1.0); }

            void main() {
                vec2 uv = gl_FragCoord.xy / uRes;
                float ar = uRes.x / max(uRes.y, 1.0);
                vec2 p = (uv - 0.5) * vec2(ar, 1.0);

                float t = uTime * 0.4;
                vec3 col = vec3(0.0);
                float totalAlpha = 0.0;

                for (int i = 0; i < 24; i++) {
                    float fi = float(i) / 23.0;
                    float offset = (fi - 0.5) * 1.8;

                    float waveX = offset + sin(p.y * 3.5 + t + fi * 4.0) * 0.28
                                         + cos(p.y * 7.0 - t * 0.7) * 0.09;
                    float dist = abs(p.x - waveX);
                    float pulse = sat(1.0 - abs(fract(p.y * 0.6 - t * 0.8 + fi * 0.2) - 0.5) * 3.5);

                    float thickness = 0.0035 + pulse * 0.005;
                    float glow = exp(-dist / (thickness * 12.0));
                    float core = exp(-dist / thickness);

                    vec3 strandCol = mix(uAccent, uHigh, fi);
                    strandCol += uBase * pulse * 1.2;

                    col += strandCol * (core + glow * 0.45);
                    totalAlpha += (core + glow * 0.35);
                }

                totalAlpha = clamp(totalAlpha * (uIsDark > 0.5 ? 0.65 : 0.48), 0.0, 0.95);
                gl_FragColor = vec4(col, totalAlpha);
            }
        `;

        const gl = canvas.getContext('webgl', { alpha: true, antialias: false, depth: false });
        if (!gl) return;

        function compile(type, src) {
            const s = gl.createShader(type);
            gl.shaderSource(s, src);
            gl.compileShader(s);
            return s;
        }

        const prog = gl.createProgram();
        gl.attachShader(prog, compile(gl.VERTEX_SHADER, VERT_SRC));
        gl.attachShader(prog, compile(gl.FRAGMENT_SHADER, FRAG_SRC));
        gl.linkProgram(prog);
        gl.useProgram(prog);

        const quad = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, quad);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);

        const posLoc = gl.getAttribLocation(prog, 'a_pos');
        gl.enableVertexAttribArray(posLoc);
        gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0);

        const uResLoc = gl.getUniformLocation(prog, 'uRes');
        const uTimeLoc = gl.getUniformLocation(prog, 'uTime');
        const uMouseLoc = gl.getUniformLocation(prog, 'uMouse');
        const uBaseLoc = gl.getUniformLocation(prog, 'uBase');
        const uAccentLoc = gl.getUniformLocation(prog, 'uAccent');
        const uHighLoc = gl.getUniformLocation(prog, 'uHigh');
        const uIsDarkLoc = gl.getUniformLocation(prog, 'uIsDark');

        gl.enable(gl.BLEND);
        gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);

        function resize() {
            const p = canvas.parentElement || document.body;
            canvas.width = Math.max(1, p.clientWidth || window.innerWidth);
            canvas.height = Math.max(1, p.clientHeight || 700);
            gl.viewport(0, 0, canvas.width, canvas.height);
        }
        resize();
        window.addEventListener('resize', resize);

        let isVisible = true;
        let animId = null;
        let startTime = performance.now();

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    isVisible = e.isIntersecting;
                    if (isVisible && !animId) render(performance.now());
                });
            }, { threshold: 0 });
            obs.observe(canvas.closest('section') || canvas.parentElement || canvas);
        }

        function render(now) {
            if (!isVisible) {
                animId = null;
                return;
            }
            animId = requestAnimationFrame(render);

            const elapsed = (now - startTime) / 1000;
            gl.clearColor(0, 0, 0, 0);
            gl.clear(gl.COLOR_BUFFER_BIT);

            gl.useProgram(prog);
            gl.uniform2f(uResLoc, canvas.width, canvas.height);
            gl.uniform1f(uTimeLoc, elapsed);
            gl.uniform2f(uMouseLoc, 0.5, 0.5);

            const dark = isDark();
            gl.uniform1f(uIsDarkLoc, dark ? 1.0 : 0.0);

            if (dark) {
                gl.uniform3f(uBaseLoc, 0.486, 0.227, 0.929);
                gl.uniform3f(uAccentLoc, 0.658, 0.333, 0.968);
                gl.uniform3f(uHighLoc, 0.753, 0.518, 0.988);
            } else {
                gl.uniform3f(uBaseLoc, 0.400, 0.150, 0.850);
                gl.uniform3f(uAccentLoc, 0.486, 0.227, 0.929);
                gl.uniform3f(uHighLoc, 0.658, 0.333, 0.968);
            }

            gl.drawArrays(gl.TRIANGLES, 0, 3);
        }

        animId = requestAnimationFrame(render);
    }

    // =========================================================================
    // 4. CAMPAIGNS BOARD: 3D PARTICLE MESH WAVE (Three.js)
    // =========================================================================
    function initWaveField() {
        const container = document.getElementById('wave-field-container');
        if (!container) return;
        if (typeof THREE === 'undefined') {
            window._waveRetries = (window._waveRetries || 0) + 1;
            if (window._waveRetries < 30) {
                setTimeout(initWaveField, 200);
            }
            return;
        }

        const isMobile = window.innerWidth < 768;
        const cols = isMobile ? 36 : 64;
        const rows = isMobile ? 24 : 40;
        const count = cols * rows;

        const scene = new THREE.Scene();
        const width = container.clientWidth || window.innerWidth;
        const height = container.clientHeight || 600;

        const camera = new THREE.PerspectiveCamera(50, width / height, 1, 2000);
        camera.position.set(0, 55, 140);
        camera.lookAt(0, -5, -20);

        const renderer = new THREE.WebGLRenderer({ antialias: false, alpha: true, powerPreference: 'high-performance' });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.2));
        renderer.setClearColor(0x000000, 0);
        container.innerHTML = '';
        container.appendChild(renderer.domElement);

        const positions = new Float32Array(count * 3);
        const colors = new Float32Array(count * 3);

        const spacingX = 4.2;
        const spacingZ = 3.8;
        const offsetX = (cols * spacingX) / 2;
        const offsetZ = (rows * spacingZ) / 2;

        let idx = 0;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                positions[idx * 3 + 0] = c * spacingX - offsetX;
                positions[idx * 3 + 1] = 0;
                positions[idx * 3 + 2] = r * spacingZ - offsetZ;

                const f = (c / cols + r / rows) * 0.5;
                colors[idx * 3 + 0] = 0.486 + f * 0.2;
                colors[idx * 3 + 1] = 0.227 + f * 0.4;
                colors[idx * 3 + 2] = 0.929 + f * 0.05;
                idx++;
            }
        }

        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        const material = new THREE.PointsMaterial({
            size: isMobile ? 2.5 : 3.8,
            vertexColors: true,
            transparent: true,
            opacity: 0.75,
            blending: THREE.NormalBlending
        });

        const points = new THREE.Points(geometry, material);
        scene.add(points);

        function resize() {
            const w = container.clientWidth || window.innerWidth;
            const h = container.clientHeight || 600;
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
            renderer.setSize(w, h);
        }
        window.addEventListener('resize', resize);

        let isVisible = true;
        let animId = null;
        let clock = 0;

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    isVisible = e.isIntersecting;
                    if (isVisible && !animId) animate();
                });
            }, { threshold: 0 });
            obs.observe(container.closest('section') || container);
        }

        function animate() {
            if (!isVisible) {
                animId = null;
                return;
            }
            animId = requestAnimationFrame(animate);

            clock += 0.022;
            const pos = geometry.attributes.position.array;
            let i = 0;
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const x = pos[i * 3 + 0];
                    const z = pos[i * 3 + 2];
                    pos[i * 3 + 1] = Math.sin(x * 0.05 + clock) * 7.0 +
                                    Math.cos(z * 0.06 + clock * 0.8) * 5.0;
                    i++;
                }
            }
            geometry.attributes.position.needsUpdate = true;
            renderer.render(scene, camera);
        }

        animate();
    }

    // =========================================================================
    // 5. TRUST SECTION: DATA STRINGS (Radiant Telemetry Rays)
    // =========================================================================
    function initTrustDataStrings() {
        const canvas = document.getElementById('data-strings-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let w = 1, h = 1;
        function resize() {
            const p = canvas.parentElement || document.body;
            w = canvas.width = p.clientWidth || window.innerWidth;
            h = canvas.height = p.clientHeight || 800;
        }
        resize();
        window.addEventListener('resize', resize);

        const strands = [];
        const count = window.innerWidth < 768 ? 40 : 80;
        for (let i = 0; i < count; i++) {
            strands.push({
                x: (i / count) * w,
                speed: 0.4 + Math.random() * 0.8,
                length: 0.35 + Math.random() * 0.65,
                hue: Math.random() > 0.4 ? 265 : 220,
                width: 1 + Math.random() * 1.5,
                phase: Math.random() * Math.PI * 2
            });
        }

        let isVisible = true;
        let animId = null;
        let t = 0;

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    isVisible = e.isIntersecting;
                    if (isVisible && !animId) loop();
                });
            }, { threshold: 0 });
            obs.observe(canvas.closest('section') || canvas.parentElement || canvas);
        }

        function loop() {
            if (!isVisible) {
                animId = null;
                return;
            }
            animId = requestAnimationFrame(loop);

            t += 0.015;
            ctx.clearRect(0, 0, w, h);

            const dark = isDark();
            const alphaBase = dark ? 0.35 : 0.28;
            const centerX = w / 2;
            const originY = 0;

            for (let i = 0; i < strands.length; i++) {
                const s = strands[i];
                const sway = Math.sin(t * s.speed + s.phase) * 35;
                const endX = s.x + sway;
                const endY = h * s.length;

                const grad = ctx.createLinearGradient(centerX, originY, endX, endY);
                if (dark) {
                    grad.addColorStop(0, `hsla(${s.hue}, 85%, 65%, ${alphaBase * 1.5})`);
                    grad.addColorStop(0.7, `hsla(${s.hue}, 80%, 55%, ${alphaBase * 0.8})`);
                    grad.addColorStop(1, `hsla(${s.hue}, 80%, 50%, 0)`);
                } else {
                    grad.addColorStop(0, `hsla(265, 80%, 55%, ${alphaBase * 1.2})`);
                    grad.addColorStop(0.7, `hsla(250, 75%, 50%, ${alphaBase * 0.6})`);
                    grad.addColorStop(1, `hsla(240, 70%, 45%, 0)`);
                }

                ctx.strokeStyle = grad;
                ctx.lineWidth = s.width;
                ctx.beginPath();
                ctx.moveTo(centerX, originY);
                ctx.bezierCurveTo(
                    centerX + (endX - centerX) * 0.4, endY * 0.3,
                    endX - sway * 0.5, endY * 0.7,
                    endX, endY
                );
                ctx.stroke();

                const beadProg = (Math.sin(t * s.speed * 2.0 + s.phase) + 1) * 0.5;
                const beadX = centerX + (endX - centerX) * beadProg;
                const beadY = endY * beadProg;

                ctx.fillStyle = dark ? '#C084FC' : '#7C3AED';
                ctx.beginPath();
                ctx.arc(beadX, beadY, s.width * 1.4, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        loop();
    }

    // =========================================================================
    // 6. FINAL CTA: AMBIENT CONSTELLATION MESH (Clean, Modern, Simple & Good Looking)
    // Replaced heavy 3D tornado with subtle floating pearl nodes & luminous connecting filaments
    // =========================================================================
    function initFinalCTAConstellation() {
        const canvas = document.getElementById('cta-ambient-canvas');
        if (!canvas) {
            // Check fallback container if old HTML exists
            const oldContainer = document.getElementById('vortex-container');
            if (oldContainer && !oldContainer.querySelector('canvas')) {
                const c = document.createElement('canvas');
                c.id = 'cta-ambient-canvas';
                c.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;';
                oldContainer.appendChild(c);
                return initFinalCTAConstellation();
            }
            return;
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let w = 0, h = 0;
        let dpr = Math.min(window.devicePixelRatio || 1, 2);

        function resize() {
            const rect = canvas.getBoundingClientRect();
            w = rect.width || canvas.parentElement.clientWidth || window.innerWidth;
            h = rect.height || canvas.parentElement.clientHeight || 520;
            canvas.width = Math.floor(w * dpr);
            canvas.height = Math.floor(h * dpr);
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(dpr, dpr);
        }
        resize();
        window.addEventListener('resize', resize);

        const isMobile = window.innerWidth < 768;
        const NODE_COUNT = isMobile ? 32 : 65;
        const MAX_CONNECT_DIST = isMobile ? 95 : 140;

        const nodes = [];
        for (let i = 0; i < NODE_COUNT; i++) {
            nodes.push({
                x: Math.random() * (w || window.innerWidth),
                y: Math.random() * (h || 520),
                vx: (Math.random() - 0.5) * 0.45,
                vy: (Math.random() - 0.5) * 0.40,
                radius: 1.6 + Math.random() * 2.2,
                colorType: Math.random() > 0.4 ? 'violet' : 'cyan',
                pulseOffset: Math.random() * Math.PI * 2,
                glowEnergy: 0
            });
        }

        const pointer = { x: -9999, y: -9999, active: false };
        const section = canvas.closest('section') || canvas.parentElement;

        if (section) {
            section.addEventListener('pointermove', (e) => {
                const r = canvas.getBoundingClientRect();
                pointer.x = e.clientX - r.left;
                pointer.y = e.clientY - r.top;
                pointer.active = true;
            });
            section.addEventListener('pointerleave', () => {
                pointer.active = false;
                pointer.x = -9999;
                pointer.y = -9999;
            });
        }

        let isVisible = true;
        let animId = null;

        if ('IntersectionObserver' in window && section) {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    isVisible = e.isIntersecting;
                    if (isVisible && !animId) frame();
                });
            }, { threshold: 0.05 });
            obs.observe(section);
        }

        let tick = 0;
        function frame() {
            if (!isVisible) {
                animId = null;
                return;
            }
            animId = requestAnimationFrame(frame);
            tick += 0.015;

            const dark = isDark();
            ctx.clearRect(0, 0, w, h);

            // Update & repel / attract nodes
            for (let i = 0; i < nodes.length; i++) {
                const n = nodes[i];
                n.x += n.vx;
                n.y += n.vy;

                // Soft wrap around bounds
                if (n.x < -20) n.x = w + 20;
                if (n.x > w + 20) n.x = -20;
                if (n.y < -20) n.y = h + 20;
                if (n.y > h + 20) n.y = -20;

                // Mouse proximity excitation
                if (pointer.active) {
                    const dx = pointer.x - n.x;
                    const dy = pointer.y - n.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 150) {
                        const factor = 1 - (dist / 150);
                        n.glowEnergy = Math.max(n.glowEnergy, factor);
                        // Gentle nudge away
                        n.x -= (dx / dist) * factor * 0.6;
                        n.y -= (dy / dist) * factor * 0.6;
                    }
                }
                n.glowEnergy *= 0.94;
            }

            // Draw connecting filament lines
            for (let i = 0; i < nodes.length; i++) {
                const na = nodes[i];
                for (let j = i + 1; j < nodes.length; j++) {
                    const nb = nodes[j];
                    const dx = na.x - nb.x;
                    const dy = na.y - nb.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < MAX_CONNECT_DIST) {
                        const alpha = (1 - (dist / MAX_CONNECT_DIST)) * (dark ? 0.22 : 0.18);
                        ctx.beginPath();
                        ctx.moveTo(na.x, na.y);
                        ctx.lineTo(nb.x, nb.y);
                        ctx.strokeStyle = dark
                            ? (na.colorType === 'cyan' ? `rgba(56, 189, 248, ${alpha})` : `rgba(168, 85, 247, ${alpha})`)
                            : (na.colorType === 'cyan' ? `rgba(2, 132, 199, ${alpha * 0.9})` : `rgba(124, 58, 237, ${alpha * 0.9})`);
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }

            // Draw floating glowing nodes
            for (let i = 0; i < nodes.length; i++) {
                const n = nodes[i];
                const pulse = 0.8 + Math.sin(tick * 2 + n.pulseOffset) * 0.25;
                const r = (n.radius + n.glowEnergy * 2.0) * pulse;

                // Halo glow when near mouse
                if (n.glowEnergy > 0.1) {
                    ctx.beginPath();
                    ctx.arc(n.x, n.y, r * 3.5, 0, Math.PI * 2);
                    ctx.fillStyle = n.colorType === 'cyan' ? 'rgba(56, 189, 248, 0.25)' : 'rgba(168, 85, 247, 0.25)';
                    ctx.fill();
                }

                // Core node
                ctx.beginPath();
                ctx.arc(n.x, n.y, r, 0, Math.PI * 2);
                if (dark) {
                    ctx.fillStyle = n.colorType === 'cyan'
                        ? `rgba(56, 189, 248, ${0.75 + n.glowEnergy * 0.25})`
                        : `rgba(192, 132, 252, ${0.80 + n.glowEnergy * 0.20})`;
                } else {
                    ctx.fillStyle = n.colorType === 'cyan'
                        ? `rgba(2, 132, 199, ${0.80 + n.glowEnergy * 0.20})`
                        : `rgba(124, 58, 237, ${0.85 + n.glowEnergy * 0.15})`;
                }
                ctx.fill();
            }
        }

        frame();
    }
    // Alias for backwards compatibility
    function initFinalCTAVortex() {
        initFinalCTAConstellation();
    }

    // =========================================================================
    // 7. MASTER BOOTSTRAP
    // =========================================================================
    function boot() {
        try { initHeroParticleField(); } catch(e) { console.warn('Hero anim:', e); }
        try { initStarfieldButtons(); } catch(e) { console.warn('Starfield anim:', e); }
        try { initArchitectureLightCables(); } catch(e) { console.warn('Cables anim:', e); }
        try { initWaveField(); } catch(e) { console.warn('Wave anim:', e); }
        try { initTrustDataStrings(); } catch(e) { console.warn('Trust anim:', e); }
        try { initFinalCTAConstellation(); } catch(e) { console.warn('CTA anim:', e); }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // Listen for dynamic elements or Alpine updates
    window.addEventListener('gezux-theme-changed', () => {
        // Theme updates handled automatically on next requestAnimationFrame
    });

    window.gezuxInitAnimations = boot;
})();
