// Starfield Button — GEZUX Ambient Dim & Prominent Hover Glow Button
import React, { useEffect, useLayoutEffect, useRef, useState } from "react";

type BandWidths = { top: number; right: number; bottom: number; left: number };
const MAX_BAND_WIDTH = 30;

const num = (v: any) => {
    const parsed = parseFloat(String(v ?? ""));
    return Number.isFinite(parsed) && parsed > 0 ? Math.min(parsed, MAX_BAND_WIDTH) : 0;
};

const bandWidthsOf = (b: any): BandWidths => {
    const fused = num(b?.borderWidth ?? 1);
    return {
        top: b?.borderTopWidth !== undefined ? num(b.borderTopWidth) : fused,
        right: b?.borderRightWidth !== undefined ? num(b.borderRightWidth) : fused,
        bottom: b?.borderBottomWidth !== undefined ? num(b.borderBottomWidth) : fused,
        left: b?.borderLeftWidth !== undefined ? num(b.borderLeftWidth) : fused,
    };
};

const borderColorOf = (b: any): string => b?.borderColor ?? "rgba(255,255,255,0.12)";

type RGBA = { r: number; g: number; b: number; a: number };
const WHITE: RGBA = { r: 255, g: 255, b: 255, a: 1 };

function parseColor(input?: string): RGBA {
    if (!input) return WHITE;
    let c = String(input).trim();
    const token = c.match(/^var\([^,]+,\s*(.+)\)$/i);
    if (token) c = token[1].trim();

    if (c[0] === "#") {
        let h = c.slice(1);
        if (h.length === 3 || h.length === 4)
            h = h.split("").map((ch) => ch + ch).join("");
        if (h.length !== 6 && h.length !== 8) return WHITE;
        const n = parseInt(h, 16);
        if (Number.isNaN(n)) return WHITE;
        return h.length === 6
            ? { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255, a: 1 }
            : {
                  r: (n >>> 24) & 255,
                  g: (n >>> 16) & 255,
                  b: (n >>> 8) & 255,
                  a: (n & 255) / 255,
              };
    }

    const fn = c.match(/rgba?\(([^)]+)\)/i);
    if (fn) {
        const p = fn[1].split(/[,\s/]+/).filter(Boolean).map(Number);
        if (p.length >= 3 && p.slice(0, 3).every((v) => !Number.isNaN(v)))
            return {
                r: p[0],
                g: p[1],
                b: p[2],
                a: p.length > 3 && !Number.isNaN(p[3]) ? p[3] : 1,
            };
    }
    return WHITE;
}

const rgba = (c: RGBA, a: number) =>
    `rgba(${Math.round(c.r)}, ${Math.round(c.g)}, ${Math.round(c.b)}, ${Math.max(0, Math.min(1, a))})`;

const radiusFromPercent = (w: number, h: number, pct: number) =>
    (Math.min(w, h) / 2) * (Math.max(0, Math.min(100, pct)) / 100);

const BAND_MASK: React.CSSProperties = {
    maskImage: "linear-gradient(#000 0 0), linear-gradient(#000 0 0)",
    maskClip: "border-box, content-box",
    maskComposite: "exclude",
    WebkitMaskImage: "linear-gradient(#000 0 0), linear-gradient(#000 0 0)",
    WebkitMaskClip: "border-box, content-box",
    WebkitMaskComposite: "xor",
} as React.CSSProperties;

const useIsoLayoutEffect = typeof window !== "undefined" ? useLayoutEffect : useEffect;

const rnd = (i: number, salt: number) => {
    const x = Math.sin((i + 1) * 12.9898 + salt * 78.233) * 43758.5453;
    return x - Math.floor(x);
};

const pointOnRoundRect = (t: number, w: number, h: number, r: number) => {
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
};

const SECONDS_AT_SPEED_1 = 8;

type Cell = {
    cx: number;
    cy: number;
    base: number;
    speed: number;
    phase: number;
};

export interface StarfieldButtonProps {
    children?: React.ReactNode;
    label?: string;
    onClick?: (e: React.MouseEvent) => void;
    variant?: "primary" | "secondary" | "brand" | "outline";
    fill?: string;
    textColor?: string;
    glowColor?: string;
    accentColor?: string;
    size?: "xs" | "sm" | "md" | "lg";
    padding?: string;
    rounded?: number;
    className?: string;
    disabled?: boolean;
    idleOpacity?: number; // Ambient dim effect when not hovering (default 0.38)
    hoverOpacity?: number; // Bright prominent effect on hover (default 1.0)
    icon?: React.ReactNode;
    iconPosition?: "left" | "right";
    style?: React.CSSProperties;
}

export const StarfieldButton: React.FC<StarfieldButtonProps> = ({
    children,
    label,
    onClick,
    variant = "primary",
    fill: customFill,
    textColor: customTextColor,
    glowColor: customGlowColor,
    accentColor: customAccentColor,
    size = "md",
    padding,
    rounded = 12,
    className = "",
    disabled = false,
    idleOpacity = 0.38,
    hoverOpacity = 1.0,
    icon,
    iconPosition = "right",
    style,
}) => {
    const buttonRef = useRef<HTMLButtonElement>(null);
    const trackRef = useRef<HTMLDivElement>(null);
    const bandRef = useRef<HTMLDivElement>(null);
    const faceRef = useRef<HTMLSpanElement>(null);
    const innerGlowRef = useRef<HTMLSpanElement>(null);
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const lightsRef = useRef<Array<HTMLDivElement | null>>([]);

    const [isHovered, setIsHovered] = useState(false);
    const [isPressed, setIsPressed] = useState(false);
    const currentRevealRef = useRef(idleOpacity);

    // Preset color palettes based on variant
    let fill = customFill ?? "#0B0B10";
    let textColor = customTextColor ?? "#F7F7F5";
    let glowColor = customGlowColor ?? "#7C3AED"; // Default Electric Violet
    let accentColor = customAccentColor ?? "#C084FC";

    if (variant === "primary") {
        glowColor = customGlowColor ?? "#8B5CF6"; // Violet glow
        accentColor = customAccentColor ?? "#DDD6FE";
        fill = customFill ?? "#0D0C18";
    } else if (variant === "brand") {
        glowColor = customGlowColor ?? "#3B82F6"; // Blue glow
        accentColor = customAccentColor ?? "#93C5FD";
        fill = customFill ?? "#0A0D18";
    } else if (variant === "secondary") {
        glowColor = customGlowColor ?? "#6B7280"; // Subtle gray/white glow
        accentColor = customAccentColor ?? "#E5E7EB";
        fill = customFill ?? "#121218";
    } else if (variant === "outline") {
        glowColor = customGlowColor ?? "#A855F7";
        accentColor = customAccentColor ?? "#E9D5FF";
        fill = customFill ?? "transparent";
    }

    // Padding presets
    const getPadding = () => {
        if (padding) return padding;
        switch (size) {
            case "xs":
                return "6px 12px";
            case "sm":
                return "7px 16px";
            case "lg":
                return "13px 28px";
            case "md":
            default:
                return "10px 22px";
        }
    };

    const getFontSize = () => {
        switch (size) {
            case "xs":
                return "text-[11px] font-semibold";
            case "sm":
                return "text-xs font-semibold";
            case "lg":
                return "text-sm sm:text-base font-bold";
            case "md":
            default:
                return "text-xs sm:text-sm font-semibold";
        }
    };

    const band = bandWidthsOf({ borderWidth: 1.5 });
    const bandPadding = `${band.top}px ${band.right}px ${band.bottom}px ${band.left}px`;
    const bandMax = Math.max(band.top, band.right, band.bottom, band.left);

    const lightCount = 2;
    const lightPx = size === "lg" ? 80 : 64;
    const lightThick = 2;
    const ringInset = bandMax / 2 - lightThick / 2;

    const geom = useRef({ w: 0, h: 0, radius: 0 });

    useIsoLayoutEffect(() => {
        const el = buttonRef.current;
        if (!el) return;
        const applyRadius = () => {
            const w = el.offsetWidth;
            const h = el.offsetHeight;
            if (!w || !h) return;
            const radius = typeof rounded === "number" ? rounded : radiusFromPercent(w, h, 100);
            geom.current = { w, h, radius };
            el.style.borderRadius = `${radius}px`;
            if (trackRef.current) trackRef.current.style.borderRadius = `${radius}px`;
            if (bandRef.current) bandRef.current.style.borderRadius = `${Math.max(0, radius - ringInset)}px`;
            if (faceRef.current) faceRef.current.style.borderRadius = `${Math.max(0, radius - bandMax)}px`;
            if (innerGlowRef.current) innerGlowRef.current.style.borderRadius = `${Math.max(0, radius - bandMax)}px`;
        };
        applyRadius();
        const ro = new ResizeObserver(applyRadius);
        ro.observe(el);
        return () => ro.disconnect();
    }, [rounded, ringInset, bandMax]);

    const sizeRef = useRef({ w: 1, h: 1, dpr: 1 });
    const city = useRef<{ cols: number; rows: number; dens: number; cells: Cell[] }>({
        cols: 0,
        rows: 0,
        dens: -1,
        cells: [],
    });

    const buildCity = (w: number, h: number, cell: number) => {
        const c = Math.max(4, Math.round(cell));
        const cols = Math.max(1, Math.floor(w / c));
        const rows = Math.max(1, Math.floor(h / c));
        const dens = 0.58;
        if (cols === city.current.cols && rows === city.current.rows && city.current.cells.length) return;
        const offX = (w - cols * c) / 2;
        const offY = (h - rows * c) / 2;
        const cells: Cell[] = [];
        for (let r = 0; r < rows; r++) {
            for (let col = 0; col < cols; col++) {
                const i = r * cols + col;
                const lit = rnd(i, 1) < dens;
                cells.push({
                    cx: offX + col * c + c / 2,
                    cy: offY + r * c + c / 2,
                    base: lit ? 0.45 + rnd(i, 2) * 0.55 : 0.08 + rnd(i, 3) * 0.15,
                    speed: 0.8 + rnd(i, 4) * 2.2,
                    phase: rnd(i, 5) * Math.PI * 2,
                });
            }
        }
        city.current = { cols, rows, dens, cells };
    };

    const draw = (ctx: CanvasRenderingContext2D) => {
        const { w, h, dpr } = sizeRef.current;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, w, h);

        const rv = currentRevealRef.current;
        if (rv < 0.001) return;

        buildCity(w, h, 4);
        const { cells } = city.current;
        const dot = 2.4;
        const off = dot / 2;
        const t = performance.now() / 1000;

        ctx.globalCompositeOperation = "lighter";
        ctx.fillStyle = accentColor;
        for (let k = 0; k < cells.length; k++) {
            const p = cells[k];
            const tw = 0.45 + 0.55 * Math.sin(t * p.speed + p.phase);
            const a = p.base * tw * rv;
            if (a <= 0.002) continue;
            ctx.globalAlpha = Math.min(1, a);
            ctx.fillRect(p.cx - off, p.cy - off, dot, dot);
        }
        ctx.globalAlpha = 1;
        ctx.globalCompositeOperation = "source-over";
    };

    const placeLights = (elapsedSec: number) => {
        const { w, h, radius } = geom.current;
        if (!w || !h) return;
        const half = bandMax / 2;
        const pw = Math.max(0, w - bandMax);
        const ph = Math.max(0, h - bandMax);
        const pr = Math.max(0, radius - half);
        const base = elapsedSec * (2.2 / SECONDS_AT_SPEED_1);

        for (let i = 0; i < lightCount; i++) {
            const el = lightsRef.current[i];
            if (!el) continue;
            let t = base + i / lightCount;
            t = ((t % 1) + 1) % 1;
            const p = pointOnRoundRect(t, pw, ph, pr);
            el.style.transform = `translate3d(${p.x + half - ringInset}px, ${p.y + half - ringInset}px, 0)`;
            el.style.opacity = String(currentRevealRef.current);
        }
    };

    useEffect(() => {
        const canvas = canvasRef.current;
        const face = faceRef.current;
        if (!canvas || !face) return;
        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        const applySize = (w: number, h: number) => {
            if (w <= 0 || h <= 0) return;
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            sizeRef.current = { w, h, dpr };
            canvas.width = Math.max(1, Math.floor(w * dpr));
            canvas.height = Math.max(1, Math.floor(h * dpr));
        };

        const ro = new ResizeObserver(() => {
            applySize(face.clientWidth, face.clientHeight);
            placeLights(performance.now() / 1000);
        });
        ro.observe(face);
        applySize(face.clientWidth, face.clientHeight);

        let isVisible = true;
        const io = new IntersectionObserver(
            ([entry]) => {
                isVisible = entry.isIntersecting;
            },
            { rootMargin: "100px" }
        );
        if (buttonRef.current) io.observe(buttonRef.current);

        let rafId: number;
        const loop = () => {
            rafId = requestAnimationFrame(loop);
            if (!isVisible && !isHovered) return;

            const target = isHovered ? hoverOpacity : idleOpacity;
            currentRevealRef.current += (target - currentRevealRef.current) * 0.14;
            
            if (innerGlowRef.current) {
                innerGlowRef.current.style.opacity = String(currentRevealRef.current * 0.85);
            }

            draw(ctx);
            placeLights(performance.now() / 1000);
        };
        rafId = requestAnimationFrame(loop);

        return () => {
            io.disconnect();
            ro.disconnect();
            cancelAnimationFrame(rafId);
        };
    }, [isHovered, idleOpacity, hoverOpacity, accentColor]);

    const lightRGB = parseColor(glowColor);

    return (
        <div
            className={`inline-grid place-items-stretch relative select-none transition-all duration-200 ${className}`}
            style={{
                transform: isPressed ? "scale(0.97)" : isHovered ? "scale(1.025)" : "scale(1)",
                ...style,
            }}
        >
            <button
                ref={buttonRef}
                type="button"
                onClick={onClick}
                disabled={disabled}
                onMouseEnter={() => setIsHovered(true)}
                onMouseLeave={() => {
                    setIsHovered(false);
                    setIsPressed(false);
                }}
                onMouseDown={() => setIsPressed(true)}
                onMouseUp={() => setIsPressed(false)}
                style={{
                    boxSizing: "border-box",
                    position: "relative",
                    padding: bandPadding,
                    border: "none",
                    background: "transparent",
                    cursor: disabled ? "not-allowed" : "pointer",
                    userSelect: "none",
                    textDecoration: "none",
                    overflow: "visible",
                    width: "100%",
                }}
            >
                {/* Track Border */}
                <div
                    ref={trackRef}
                    aria-hidden
                    style={{
                        position: "absolute",
                        inset: 0,
                        boxSizing: "border-box",
                        padding: bandPadding,
                        background: borderColorOf({ 
                            borderColor: isHovered 
                                ? `${glowColor}66` 
                                : "rgba(255,255,255,0.12)" 
                        }),
                        zIndex: 0,
                        pointerEvents: "none",
                        transition: "background 0.3s ease",
                        ...BAND_MASK,
                    }}
                />

                {/* Rotating Glowing Light Beam Around Border */}
                <div
                    ref={bandRef}
                    aria-hidden
                    style={{
                        position: "absolute",
                        top: ringInset,
                        right: ringInset,
                        bottom: ringInset,
                        left: ringInset,
                        boxSizing: "border-box",
                        padding: lightThick,
                        zIndex: 0,
                        pointerEvents: "none",
                        ...BAND_MASK,
                    }}
                >
                    {Array.from({ length: lightCount }, (_, i) => (
                        <div
                            key={i}
                            ref={(el) => {
                                lightsRef.current[i] = el;
                            }}
                            style={{
                                position: "absolute",
                                top: 0,
                                left: 0,
                                width: lightPx,
                                height: lightPx,
                                marginTop: -lightPx / 2,
                                marginLeft: -lightPx / 2,
                                pointerEvents: "none",
                                background: `radial-gradient(circle, ${glowColor} 0%, ${glowColor} 35%, ${rgba(lightRGB, 0)} 75%)`,
                            }}
                        />
                    ))}
                </div>

                {/* Face Content Container */}
                <span
                    ref={faceRef}
                    className={`relative z-10 box-border w-full h-full flex items-center justify-center gap-2 font-display ${getFontSize()}`}
                    style={{
                        padding: getPadding(),
                        whiteSpace: "nowrap",
                        background: fill,
                        color: textColor,
                        overflow: "hidden",
                    }}
                >
                    {/* Background Canvas Starfield */}
                    <canvas
                        ref={canvasRef}
                        aria-hidden
                        style={{
                            position: "absolute",
                            inset: 0,
                            zIndex: 0,
                            width: "100%",
                            height: "100%",
                            pointerEvents: "none",
                        }}
                    />

                    {/* Inner Edge Glow (Dim in idle, brilliant on hover) */}
                    <span
                        ref={innerGlowRef}
                        aria-hidden
                        style={{
                            position: "absolute",
                            inset: 0,
                            zIndex: 1,
                            border: `1.5px solid ${glowColor}`,
                            filter: "blur(5px)",
                            opacity: idleOpacity * 0.7,
                            pointerEvents: "none",
                            maskImage: "linear-gradient(#000 0 0)",
                            maskClip: "border-box",
                            WebkitMaskImage: "linear-gradient(#000 0 0)",
                            WebkitMaskClip: "border-box",
                        }}
                    />

                    {/* Button Text & Icon */}
                    {icon && iconPosition === "left" && (
                        <span className="relative z-10 flex items-center shrink-0">
                            {icon}
                        </span>
                    )}

                    <span className="relative z-10 font-medium tracking-wide">
                        {children || label}
                    </span>

                    {icon && iconPosition === "right" && (
                        <span className="relative z-10 flex items-center shrink-0 transition-transform duration-200">
                            {icon}
                        </span>
                    )}
                </span>
            </button>
        </div>
    );
};

export default StarfieldButton;
