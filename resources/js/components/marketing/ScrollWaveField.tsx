// Scroll Wave Field — Three.js 3D Scroll-Driven Particle & Mesh Wave Field (High Performance & Responsive)
"use client";

import React, { useEffect, useRef } from "react";
import * as THREE from "three";

export interface ScrollWaveFieldProps {
  colors?: string[];
  gridCols?: number;
  gridRows?: number;
  waveSpeed?: number;
  waveHeight?: number;
  pointSize?: number;
  showLines?: boolean;
  scrollSensitivity?: number;
  opacity?: number;
  className?: string;
}

export const ScrollWaveField: React.FC<ScrollWaveFieldProps> = ({
  colors = ["#7C3AED", "#38BDF8", "#A855F7", "#6366F1"],
  gridCols: propCols = 80,
  gridRows: propRows = 50,
  waveSpeed = 1.3,
  waveHeight = 26,
  pointSize = 3.5,
  showLines = true,
  scrollSensitivity = 0.08,
  opacity = 0.65,
  className = "",
}) => {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container || typeof window === "undefined") return;

    // Mobile adaptation: Scale down grid resolution on mobile for 60fps
    const isMobile = window.innerWidth < 768;
    const gridCols = isMobile ? Math.min(propCols, 45) : propCols;
    const gridRows = isMobile ? Math.min(propRows, 30) : propRows;

    // Scene, Camera, Renderer
    const scene = new THREE.Scene();
    const width = container.clientWidth || window.innerWidth;
    const height = container.clientHeight || 600;

    const camera = new THREE.PerspectiveCamera(55, width / height, 1, 3000);
    camera.position.set(0, 75, 160);
    camera.lookAt(0, -10, -30);

    const renderer = new THREE.WebGLRenderer({
      antialias: !isMobile,
      alpha: true,
      powerPreference: "high-performance",
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);

    // Fog for depth fading
    scene.fog = new THREE.FogExp2(0x070708, 0.0035);

    // Parse Colors
    const palette = colors.map((c) => new THREE.Color(c));

    // Particle Grid Geometry
    const count = gridCols * gridRows;
    const planeWidth = 320;
    const planeDepth = 240;

    const positions = new Float32Array(count * 3);
    const originalPositions = new Float32Array(count * 3);
    const particleColors = new Float32Array(count * 3);
    const phases = new Float32Array(count);

    let idx = 0;
    for (let r = 0; r < gridRows; r++) {
      const z = (r / (gridRows - 1) - 0.5) * planeDepth - 20;
      for (let c = 0; c < gridCols; c++) {
        const x = (c / (gridCols - 1) - 0.5) * planeWidth;
        const y = 0;

        positions[idx * 3] = x;
        positions[idx * 3 + 1] = y;
        positions[idx * 3 + 2] = z;

        originalPositions[idx * 3] = x;
        originalPositions[idx * 3 + 1] = y;
        originalPositions[idx * 3 + 2] = z;

        // Color gradient along grid
        const colRatio = c / (gridCols - 1);
        const rowRatio = r / (gridRows - 1);
        const colorIndex = (colRatio + rowRatio * 0.5) * (palette.length - 1);
        const c1 = palette[Math.floor(colorIndex) % palette.length];
        const c2 = palette[Math.ceil(colorIndex) % palette.length];
        const blend = colorIndex - Math.floor(colorIndex);
        const finalCol = c1.clone().lerp(c2, blend);

        particleColors[idx * 3] = finalCol.r;
        particleColors[idx * 3 + 1] = finalCol.g;
        particleColors[idx * 3 + 2] = finalCol.b;

        phases[idx] = Math.sin(c * 0.3) * Math.cos(r * 0.3) * Math.PI;
        idx++;
      }
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute("position", new THREE.BufferAttribute(positions, 3));
    geometry.setAttribute("color", new THREE.BufferAttribute(particleColors, 3));

    // Glow dot circle texture
    const createCircleTexture = () => {
      const canvas = document.createElement("canvas");
      canvas.width = 32;
      canvas.height = 32;
      const ctx = canvas.getContext("2d");
      if (ctx) {
        const grad = ctx.createRadialGradient(16, 16, 0, 16, 16, 16);
        grad.addColorStop(0, "rgba(255,255,255,1)");
        grad.addColorStop(0.3, "rgba(255,255,255,0.85)");
        grad.addColorStop(0.7, "rgba(255,255,255,0.25)");
        grad.addColorStop(1, "rgba(255,255,255,0)");
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, 32, 32);
      }
      return new THREE.CanvasTexture(canvas);
    };

    const pointTexture = createCircleTexture();

    const pointMaterial = new THREE.PointsMaterial({
      size: isMobile ? pointSize * 1.3 : pointSize,
      vertexColors: true,
      map: pointTexture,
      transparent: true,
      opacity: 0.92,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });

    const points = new THREE.Points(geometry, pointMaterial);
    scene.add(points);

    // Optional Connecting Line Grid
    let lineMesh: THREE.LineSegments | null = null;
    let linePositions: Float32Array;

    if (showLines) {
      const lineIndices: number[] = [];
      for (let r = 0; r < gridRows; r++) {
        for (let c = 0; c < gridCols; c++) {
          const current = r * gridCols + c;
          if (c < gridCols - 1) {
            lineIndices.push(current, current + 1);
          }
          if (r < gridRows - 1) {
            lineIndices.push(current, current + gridCols);
          }
        }
      }

      const lineGeo = new THREE.BufferGeometry();
      linePositions = new Float32Array(positions.length);
      linePositions.set(positions);
      lineGeo.setAttribute("position", new THREE.BufferAttribute(linePositions, 3));
      lineGeo.setIndex(lineIndices);

      const lineMat = new THREE.LineBasicMaterial({
        color: new THREE.Color("#7C3AED"),
        transparent: true,
        opacity: 0.16,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
      });

      lineMesh = new THREE.LineSegments(lineGeo, lineMat);
      scene.add(lineMesh);
    }

    // Scroll & Mouse listeners
    let targetScrollY = window.scrollY;
    let currentScrollY = window.scrollY;
    let mouseX = 0;
    let mouseY = 0;
    let targetMouseX = 0;
    let targetMouseY = 0;
    let isVisible = true;

    const handleScroll = () => {
      targetScrollY = window.scrollY;
    };

    const handlePointerMove = (e: MouseEvent | TouchEvent) => {
      const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX;
      const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY;
      const rect = container.getBoundingClientRect();
      const x = ((clientX - rect.left) / rect.width) * 2 - 1;
      const y = -(((clientY - rect.top) / rect.height) * 2 - 1);
      targetMouseX = x * 25;
      targetMouseY = y * 15;
    };

    window.addEventListener("scroll", handleScroll, { passive: true });
    window.addEventListener("mousemove", handlePointerMove, { passive: true });
    window.addEventListener("touchmove", handlePointerMove, { passive: true });

    // Intersection Observer to pause rendering when offscreen (High Performance)
    const io = new IntersectionObserver(
      ([entry]) => {
        isVisible = entry.isIntersecting;
      },
      { rootMargin: "150px" }
    );
    io.observe(container);

    // Resize Observer
    const handleResize = () => {
      if (!container) return;
      const w = container.clientWidth || window.innerWidth;
      const h = container.clientHeight || 600;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    };

    const ro = new ResizeObserver(handleResize);
    ro.observe(container);

    // Animation Loop
    let clock = new THREE.Clock();
    let animId: number;

    const animate = () => {
      animId = requestAnimationFrame(animate);

      // Skip render calculations when off-screen
      if (!isVisible) return;

      const elapsedTime = clock.getElapsedTime() * waveSpeed;
      
      // Smooth scroll interpolation
      currentScrollY += (targetScrollY - currentScrollY) * 0.08;
      const scrollOffset = currentScrollY * scrollSensitivity;

      // Smooth mouse interpolation
      mouseX += (targetMouseX - mouseX) * 0.05;
      mouseY += (targetMouseY - mouseY) * 0.05;

      camera.position.x = mouseX * 0.5;
      camera.position.y = 75 + mouseY * 0.4;
      camera.lookAt(0, -10, -30);

      const posAttr = geometry.attributes.position as THREE.BufferAttribute;
      const posArray = posAttr.array as Float32Array;

      for (let i = 0; i < count; i++) {
        const ox = originalPositions[i * 3];
        const oz = originalPositions[i * 3 + 2];
        const phase = phases[i];

        // Wave Equations: Main sinusoidal wave + Cross harmonics + Scroll wave flow
        const wave1 = Math.sin(ox * 0.04 + elapsedTime * 1.5 + scrollOffset * 0.05 + phase);
        const wave2 = Math.cos(oz * 0.05 + elapsedTime * 1.2 - scrollOffset * 0.04);
        const wave3 = Math.sin((ox + oz) * 0.03 + elapsedTime * 0.8) * 0.5;

        // Interactive mouse dip near center
        const distToMouse = Math.hypot(ox - mouseX * 3, oz - mouseY * 3);
        const mouseRipple = Math.exp(-distToMouse * 0.02) * Math.sin(distToMouse * 0.1 - elapsedTime * 3) * 6;

        const y = (wave1 * wave2 + wave3) * waveHeight + mouseRipple;
        posArray[i * 3 + 1] = y;

        if (lineMesh) {
          linePositions[i * 3 + 1] = y;
        }
      }

      posAttr.needsUpdate = true;
      if (lineMesh) {
        (lineMesh.geometry.attributes.position as THREE.BufferAttribute).needsUpdate = true;
      }

      renderer.render(scene, camera);
    };

    animate();

    // Cleanup
    return () => {
      cancelAnimationFrame(animId);
      window.removeEventListener("scroll", handleScroll);
      window.removeEventListener("mousemove", handlePointerMove);
      window.removeEventListener("touchmove", handlePointerMove);
      io.disconnect();
      ro.disconnect();
      if (renderer.domElement.parentNode) {
        renderer.domElement.parentNode.removeChild(renderer.domElement);
      }
      geometry.dispose();
      pointMaterial.dispose();
      pointTexture.dispose();
      if (lineMesh) {
        lineMesh.geometry.dispose();
        (lineMesh.material as THREE.Material).dispose();
      }
      renderer.dispose();
    };
  }, [
    colors,
    propCols,
    propRows,
    waveSpeed,
    waveHeight,
    pointSize,
    showLines,
    scrollSensitivity,
  ]);

  return (
    <div
      ref={containerRef}
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none z-0 ${className}`}
      style={{ opacity }}
    />
  );
};

export default ScrollWaveField;
