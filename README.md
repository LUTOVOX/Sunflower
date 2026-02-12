      letter-spacing: 2px;
    }
    .message-card p {
      font-size: 1.7rem;
      margin: 12px 0 4px;
      color: #ffebc2;
      font-weight: 400;
      text-shadow: 0 0 12px #ff8c1a;
    }
    .signature {
      font-size: 2rem;
      font-weight: 700;
      margin-top: 16px;
      color: #ffe083;
      text-shadow: 0 0 20px #ffaa00, 0 0 50px #ff5500;
      font-style: italic;
      border-top: 2px dashed #ffb347;
      display: inline-block;
      padding-top: 12px;
    }
    @keyframes floatSoft {
      0% { transform: translateY(0px) rotate(-0.7deg); }
      100% { transform: translateY(-15px) rotate(0.3deg); }
    }
    @keyframes messageAppear {
      0% { opacity: 0; transform: scale(0.8) translateY(60px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    /* corner badges — delicate art */
    .corner-badge {
      position: absolute;
      bottom: 30px;
      right: 30px;
      z-index: 150;
      color: rgba(255,245,200,0.9);
      background: rgba(40,25,10,0.5);
      backdrop-filter: blur(8px);
      padding: 14px 26px;
      border-radius: 60px;
      border: 1px solid #ffcc77;
      font-size: 1.3rem;
      font-weight: 500;
      letter-spacing: 3px;
      box-shadow: 0 0 30px rgba(255,200,0,0.3);
      animation: glowPulse 4s infinite;
    }
    .corner-badge i {
      color: #ffb347;
      margin-right: 8px;
    }
    @keyframes glowPulse {
      0% { box-shadow: 0 0 20px rgba(255,200,100,0.3); }
      50% { box-shadow: 0 0 50px rgba(255,150,0,0.6); }
      100% { box-shadow: 0 0 20px rgba(255,200,100,0.3); }
    }
    /* tiny responsive */
    @media (max-width: 700px) {
      .message-card h1 { font-size: 2.3rem; }
      .message-card p { font-size: 1.2rem; }
      .signature { font-size: 1.5rem; }
      .message-card { padding: 18px 28px; }
    }
  </style>
</head>
<body>
  <div id="canvas-container"></div>

  <!-- MOTION TYPOGRAPHY — precious poetry for Manisha -->
  <div class="valentine-message">
    <div class="message-card">
      <h1>🌻 for Manisha</h1>
      <p>you are my golden hour, my forever summer</p>
      <div class="signature">— your Lutom 💞</div>
    </div>
  </div>
  
  <div class="corner-badge">
    <i>✨</i> sunflower garden · valentine motion art
  </div>

  <!-- THREE.JS with all addons -->
  <script type="importmap">
    {
      "imports": {
        "three": "https://unpkg.com/three@0.128.0/build/three.module.js",
        "three/addons/": "https://unpkg.com/three@0.128.0/examples/jsm/"
      }
    }
  </script>

  <script type="module">
    import * as THREE from 'three';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
    
    // ---------- DREAMSCAPE SETUP · CINEMATIC ----------
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a1520); // deep romantic blue
    
    // VOLUMETRIC FOG — adds depth and mystery
    scene.fog = new THREE.FogExp2(0x0a1520, 0.025);
    
    const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.set(9, 5, 18);
    camera.lookAt(4, 2.2, 0);
    
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.toneMapping = THREE.ReinhardToneMapping;
    renderer.toneMappingExposure = 1.4;
    document.getElementById('canvas-container').appendChild(renderer.domElement);
    
    // ---------- ORBIT CONTROLS with auto-rotation, smooth cinematic motion ----------
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.06;
    controls.autoRotate = true;
    controls.autoRotateSpeed = 0.65;
    controls.enableZoom = true;
    controls.enablePan = false;
    controls.enableRotate = true;
    controls.rotateSpeed = 0.7;
    controls.target.set(4, 2.2, 0);
    controls.maxPolarAngle = Math.PI / 2.3;
    controls.minDistance = 14;
    controls.maxDistance = 35;
    
    // ---------- ENCHANTED LIGHTING ----------
    const ambient = new THREE.AmbientLight(0x40556e);
    scene.add(ambient);
    
    // main golden sun
    const sunLight = new THREE.DirectionalLight(0xffeed5, 1.4);
    sunLight.position.set(7, 12, 10);
    sunLight.castShadow = true;
    sunLight.receiveShadow = true;
    sunLight.shadow.mapSize.width = 2048;
    sunLight.shadow.mapSize.height = 2048;
    sunLight.shadow.camera.near = 0.5;
    sunLight.shadow.camera.far = 50;
    sunLight.shadow.camera.left = -15;
    sunLight.shadow.camera.right = 15;
    sunLight.shadow.camera.top = 15;
    sunLight.shadow.camera.bottom = -15;
    scene.add(sunLight);
    
    // romantic backlight - pinkish glow
    const backLight = new THREE.PointLight(0xffaacc, 0.8);
    backLight.position.set(-6, 4, -12);
    scene.add(backLight);
    
    // fill lights for garden
    const fillLight1 = new THREE.PointLight(0xffcc88, 0.6);
    fillLight1.position.set(2, 3, 10);
    scene.add(fillLight1);
    
    const fillLight2 = new THREE.PointLight(0x88aaff, 0.4);
    fillLight2.position.set(-3, 2, 15);
    scene.add(fillLight2);
    
    // ground glow — warm earth
    const groundGlow = new THREE.PointLight(0xcc8844, 0.5);
    groundGlow.position.set(4, 0.5, 1);
    scene.add(groundGlow);
    
    // ---------- GROUND: rolling meadow ----------
    const groundGeo = new THREE.CircleGeometry(70, 64);
    const groundMat = new THREE.MeshStandardMaterial({
        color: 0x3a5a3a,
        roughness: 0.9,
        metalness: 0.1,
        emissive: 0x1a2a1a,
        emissiveIntensity: 0.15,
        side: THREE.DoubleSide
    });
    const ground = new THREE.Mesh(groundGeo, groundMat);
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -0.1;
    ground.position.x = 4;
    ground.position.z = 0;
    ground.receiveShadow = true;
    scene.add(ground);
    
    // --- additional soft meadow patches for depth ---
    const meadowPatchMat = new THREE.MeshStandardMaterial({ color: 0x4a6e4a, roughness: 0.8, emissive: 0x1e321e, emissiveIntensity: 0.1 });
    for (let i=0;i<12;i++) {
      const patch = new THREE.Mesh(new THREE.CircleGeometry(5+Math.random()*8, 8), meadowPatchMat.clone());
      patch.rotation.x = -Math.PI/2;
      patch.position.x = 2 + (Math.random()-0.5)*20;
      patch.position.z = (Math.random()-0.5)*25;
      patch.position.y = -0.09;
      patch.receiveShadow = true;
      scene.add(patch);
    }
    
    // ---------- 3D MOTION GARDEN · SUNFLOWERS, TREES, SCENERY ----------
    
    // *** GROUP FOR ALL PLANTS ***
    const gardenGroup = new THREE.Group();
    gardenGroup.position.set(4, 0, 0);
    
    // ----- TREES : elegant, stylized 3D trees with motion -----
    function createTree(posX, posZ, size = 1) {
      const treeGroup = new THREE.Group();
      treeGroup.position.set(posX, 0, posZ);
      
      // trunk
      const trunkMat = new THREE.MeshStandardMaterial({ color: 0x6b4e3a, roughness: 0.8, emissive: 0x321e14, emissiveIntensity: 0.1 });
      const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.25 * size, 0.35 * size, 1.8 * size, 6), trunkMat);
      trunk.position.y = 0.9 * size;
      trunk.castShadow = true;
      trunk.receiveShadow = true;
      treeGroup.add(trunk);
      
      // foliage - three layers of glossy leaves
      const foliageMat1 = new THREE.MeshStandardMaterial({ color: 0x3c8d5e, emissive: 0x1e3a1e, emissiveIntensity: 0.25, roughness: 0.5, metalness: 0.2 });
      const foliageMat2 = new THREE.MeshStandardMaterial({ color: 0x4ca64c, emissive: 0x1e3a1e, emissiveIntensity: 0.2, roughness: 0.5 });
      const foliageMat3 = new THREE.MeshStandardMaterial({ color: 0x5cb85c, emissive: 0x1e3a1e, emissiveIntensity: 0.15, roughness: 0.5 });
      
      const foliage1 = new THREE.Mesh(new THREE.ConeGeometry(0.9 * size, 0.9 * size, 8), foliageMat1);
      foliage1.position.y = 1.7 * size;
      foliage1.castShadow = true;
      foliage1.receiveShadow = true;
      treeGroup.add(foliage1);
      
      const foliage2 = new THREE.Mesh(new THREE.ConeGeometry(0.7 * size, 0.8 * size, 8), foliageMat2);
      foliage2.position.y = 2.2 * size;
      foliage2.castShadow = true;
      foliage2.receiveShadow = true;
      treeGroup.add(foliage2);
      
      const foliage3 = new THREE.Mesh(new THREE.ConeGeometry(0.5 * size, 0.7 * size, 8), foliageMat3);
      foliage3.position.y = 2.7 * size;
      foliage3.castShadow = true;
      foliage3.receiveShadow = true;
      treeGroup.add(foliage3);
      
      // add some small apples or blossoms? delicate
      const blossomMat = new THREE.MeshStandardMaterial({ color: 0xffaaaa, emissive: 0x552222, emissiveIntensity: 0.3 });
      for (let i=0; i<6; i++) {
        const blossom = new THREE.Mesh(new THREE.SphereGeometry(0.06 * size, 6), blossomMat.clone());
        blossom.position.set(
          (Math.random()-0.5)*0.8,
          2.0 * size + Math.random()*0.8,
          (Math.random()-0.5)*0.8
        );
        blossom.castShadow = true;
        treeGroup.add(blossom);
      }
      
      return treeGroup;
    }
    
    // place trees around the garden — enchanted forest vibe
    const treePositions = [
      [-2, -5], [7, 4], [-3, 7], [10, -3], [0, 9], [11, 2], [-4, -4], [6, -6], [12, 7], [-1, -8]
    ];
    treePositions.forEach(pos => {
      const tree = createTree(pos[0], pos[1], 0.9 + Math.random()*0.3);
      gardenGroup.add(tree);
    });
    
    // ----- SUNFLOWERS : main golden characters -----
    function createSunflower(posX, posZ, sizeScale = 1) {
      const fGroup = new THREE.Group();
      fGroup.position.set(posX, 0, posZ);
      
      // stem
      const stemMat = new THREE.MeshStandardMaterial({ color: 0x6b8e23, roughness: 0.7, emissive: 0x223300, emissiveIntensity: 0.15 });
      const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.12 * sizeScale, 0.15 * sizeScale, 1.4 * sizeScale, 6), stemMat);
      stem.position.y = 0.7 * sizeScale;
      stem.castShadow = true;
      stem.receiveShadow = true;
      fGroup.add(stem);
      
      // leaves
      const leafMat = new THREE.MeshStandardMaterial({ color: 0x4caf50, roughness: 0.6, emissive: 0x1e3a1e, emissiveIntensity: 0.2, side: THREE.DoubleSide });
      const leafL = new THREE.Mesh(new THREE.SphereGeometry(0.2 * sizeScale, 6), leafMat.clone());
      leafL.scale.set(0.9, 0.15, 0.4);
      leafL.position.set(-0.4 * sizeScale, 0.9 * sizeScale, 0.1);
      leafL.rotation.z = -0.6;
      leafL.rotation.x = 0.3;
      leafL.castShadow = true;
      fGroup.add(leafL);
      
      const leafR = new THREE.Mesh(new THREE.SphereGeometry(0.2 * sizeScale, 6), leafMat.clone());
      leafR.scale.set(0.9, 0.15, 0.4);
      leafR.position.set(0.4 * sizeScale, 1.1 * sizeScale, -0.1);
      leafR.rotation.z = 0.7;
      leafR.rotation.x = -0.2;
      leafR.castShadow = true;
      fGroup.add(leafR);
      
      // flower head
      const diskMat = new THREE.MeshStandardMaterial({ color: 0x8b4513, roughness: 0.5, emissive: 0x4a2c1a, emissiveIntensity: 0.25 });
      const disk = new THREE.Mesh(new THREE.SphereGeometry(0.45 * sizeScale, 16, 8), diskMat);
      disk.scale.set(1.1, 0.45, 0.9);
      disk.position.y = 1.5 * sizeScale;
      disk.castShadow = true;
      disk.receiveShadow = true;
      fGroup.add(disk);
      
      // seeds
      const seedMat = new THREE.MeshStandardMaterial({ color: 0xc49a2b, emissive: 0x553300, emissiveIntensity: 0.3 });
      for (let i=0; i<40; i++) {
        const seed = new THREE.Mesh(new THREE.SphereGeometry(0.03 + Math.random()*0.03, 4), seedMat.clone());
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.random() * Math.PI * 0.4;
        const r = 0.4 * sizeScale;
        seed.position.set(
          Math.sin(theta) * Math.sin(phi) * r * 1.1,
          1.5 * sizeScale + Math.cos(phi) * 0.25,
          Math.cos(theta) * Math.sin(phi) * r * 0.9
        );
        seed.castShadow = true;
        fGroup.add(seed);
      }
      
      // petals
      const petalMat = new THREE.MeshStandardMaterial({ color: 0xffc125, emissive: 0xcc7700, emissiveIntensity: 0.25, side: THREE.DoubleSide, roughness: 0.4 });
      for (let i=0; i<16; i++) {
        const angle = (i/16)*Math.PI*2;
        const petal = new THREE.Mesh(new THREE.SphereGeometry(0.2 * sizeScale, 6), petalMat.clone());
        petal.scale.set(0.7, 0.1, 1.1);
        petal.position.set(Math.sin(angle)*0.85 * sizeScale, 1.55 * sizeScale, Math.cos(angle)*0.85 * sizeScale);
        petal.rotation.y = angle;
        petal.rotation.x = -0.25;
        petal.rotation.z = 0.2;
        petal.material.color.setHSL(0.12, 0.9, 0.58);
        petal.castShadow = true;
        fGroup.add(petal);
        
        // inner petal
        if (i%2===0) {
          const ipetal = new THREE.Mesh(new THREE.SphereGeometry(0.16 * sizeScale, 6), petalMat.clone());
          ipetal.scale.set(0.6, 0.1, 0.9);
          ipetal.position.set(Math.sin(angle)*0.55 * sizeScale, 1.6 * sizeScale, Math.cos(angle)*0.55 * sizeScale);
          ipetal.rotation.y = angle;
          ipetal.rotation.x = -0.1;
          ipetal.rotation.z = 0.3;
          ipetal.material.color.setHSL(0.1, 0.85, 0.65);
          ipetal.castShadow = true;
          fGroup.add(ipetal);
        }
      }
      
      // tiny heart in the center (signature)
      const tinyHeart = new THREE.Group();
      tinyHeart.position.y = 1.6 * sizeScale;
      tinyHeart.position.z = 0.1 * sizeScale;
      const hMat = new THREE.MeshStandardMaterial({ color: 0xff4d4d, emissive: 0x992222, emissiveIntensity: 0.7 });
      const h1 = new THREE.Mesh(new THREE.SphereGeometry(0.06, 6), hMat);
      h1.position.set(-0.05, 0.03, 0.05);
      tinyHeart.add(h1);
      const h2 = new THREE.Mesh(new THREE.SphereGeometry(0.06, 6), hMat);
      h2.position.set(0.05, 0.03, 0.05);
      tinyHeart.add(h2);
      const h3 = new THREE.Mesh(new THREE.ConeGeometry(0.07, 0.1, 6), hMat);
      h3.rotation.x = Math.PI;
      h3.position.set(0, -0.03, 0.08);
      tinyHeart.add(h3);
      fGroup.add(tinyHeart);
      
      return fGroup;
    }
    
    // plant a field of sunflowers
    const sunflowerSpots = [
      [2, 1], [2.8, -1.2], [1, -2], [3.5, 2.5], [0.5, 2], [4, 0.5], [1.5, -1], [3, -0.5], [0, 0.5], [5, 1.2], [0.8, -2.5], [4.5, -1]
    ];
    sunflowerSpots.forEach(pos => {
      const sf = createSunflower(pos[0], pos[1], 0.9 + Math.random()*0.25);
      gardenGroup.add(sf);
    });
    
    // add the main majestic sunflower (larger) at center
    const mainFlower = createSunflower(2.2, 0.8, 1.3);
    gardenGroup.add(mainFlower);
    
    // ----- 3D GRASS FIELD : thousands of blades with motion -----
    const grassField = new THREE.Group();
    const grassMat1 = new THREE.MeshStandardMaterial({ color: 0x4a784a, emissive: 0x1e3a1e, emissiveIntensity: 0.1, roughness: 0.8, side: THREE.DoubleSide });
    const grassMat2 = new THREE.MeshStandardMaterial({ color: 0x3a683a, emissive: 0x1a2e1a, emissiveIntensity: 0.1, roughness: 0.8, side: THREE.DoubleSide });
    const grassMat3 = new THREE.MeshStandardMaterial({ color: 0x5a865a, emissive: 0x1e3a1e, emissiveIntensity: 0.1, roughness: 0.8, side: THREE.DoubleSide });
    
    for (let i=0; i<500; i++) {
      // distribute across meadow
      const radius = 5 + Math.random() * 16;
      const angle = Math.random() * Math.PI * 2;
      const x = Math.cos(angle) * radius;
      const z = Math.sin(angle) * radius;
      
      const bladeGroup = new THREE.Group();
      bladeGroup.position.set(x, 0, z);
      
      const h = 0.2 + Math.random() * 0.7;
      const blade = new THREE.Mesh(new THREE.ConeGeometry(0.04, h, 4), [grassMat1, grassMat2, grassMat3][Math.floor(Math.random()*3)].clone());
      blade.rotation.x = Math.PI / 2;
      blade.rotation.z = (Math.random() - 0.5) * 0.5;
      blade.position.y = h/2;
      blade.castShadow = true;
      blade.receiveShadow = true;
      bladeGroup.add(blade);
      
      // add an extra tiny blade
      if (Math.random()>0.6) {
        const blade2 = new THREE.Mesh(new THREE.ConeGeometry(0.025, h*0.7, 4), grassMat1.clone());
        blade2.rotation.x = Math.PI/2;
        blade2.rotation.z = (Math.random()-0.5)*0.4;
        blade2.position.y = h*0.4;
        blade2.position.x = 0.07;
        blade2.castShadow = true;
        bladeGroup.add(blade2);
      }
      
      // store random phase for animation
      bladeGroup.userData = { speed: 0.5 + Math.random() * 0.8, offset: Math.random() * 10 };
      grassField.add(bladeGroup);
    }
    gardenGroup.add(grassField);
    
    // ---- wildflowers / tiny blossoms ----
    const blossomField = new THREE.Group();
    for (let i=0; i<120; i++) {
      const radius = 4 + Math.random()*14;
      const ang = Math.random()*Math.PI*2;
      const x = Math.cos(ang)*radius;
      const z = Math.sin(ang)*radius;
      
      const col = new THREE.Color().setHSL(Math.random()*0.15 + 0.05, 0.7, 0.6);
      const flowerMat = new THREE.MeshStandardMaterial({ color: col, emissive: 0x331100, emissiveIntensity: 0.2 });
      const f = new THREE.Mesh(new THREE.SphereGeometry(0.07, 5), flowerMat);
      f.position.set(x, 0.1, z);
      f.castShadow = true;
      blossomField.add(f);
      
      // mini petals
      for (let j=0; j<4; j++) {
        const pet = new THREE.Mesh(new THREE.SphereGeometry(0.03, 4), new THREE.MeshStandardMaterial({ color: 0xffddbb, emissive: 0x442211, emissiveIntensity: 0.1 }));
        pet.position.set(x + Math.sin(j*1.57)*0.12, 0.13, z + Math.cos(j*1.57)*0.12);
        blossomField.add(pet);
      }
    }
    gardenGroup.add(blossomField);
    
    scene.add(gardenGroup);
    
    // ---------- HEAVENLY ELEMENTS: floating hearts, fireflies, stars ----------
    const particleGroup = new THREE.Group();
    
    // floating hearts
    for (let i=0; i<50; i++) {
      const heart = new THREE.Group();
      heart.position.set(
        (Math.random() - 0.5) * 30 + 4,
        2 + Math.random() * 12,
        (Math.random() - 0.5) * 30
      );
      
      const hMat = new THREE.MeshStandardMaterial({ color: 0xff6b8b, emissive: 0x551122, emissiveIntensity: 0.6, transparent: true, opacity: 0.9 });
      const h1 = new THREE.Mesh(new THREE.SphereGeometry(0.12, 6), hMat.clone());
      h1.position.set(-0.1, 0.1, 0);
      heart.add(h1);
      const h2 = new THREE.Mesh(new THREE.SphereGeometry(0.12, 6), hMat.clone());
      h2.position.set(0.1, 0.1, 0);
      heart.add(h2);
      const h3 = new THREE.Mesh(new THREE.ConeGeometry(0.13, 0.18, 6), hMat.clone());
      h3.rotation.x = Math.PI;
      h3.position.set(0, -0.06, 0);
      heart.add(h3);
      
      heart.userData = { speed: 0.2 + Math.random()*0.3, offset: Math.random()*10, baseY: heart.position.y };
      particleGroup.add(heart);
    }
    
    // golden sparkles / fireflies
    const sparkleMat = new THREE.PointsMaterial({
      color: 0xffcc88,
      size: 0.2,
      transparent: true,
      blending: THREE.AdditiveBlending,
      depthWrite: false
    });
    const sparkleGeo = new THREE.BufferGeometry();
    const sparkleCount = 300;
    const sparklePositions = new Float32Array(sparkleCount * 3);
    for (let i=0; i<sparkleCount*3; i+=3) {
      sparklePositions[i] = (Math.random() - 0.5) * 50 + 4;
      sparklePositions[i+1] = Math.random() * 15 + 2;
      sparklePositions[i+2] = (Math.random() - 0.5) * 50;
    }
    sparkleGeo.setAttribute('position', new THREE.BufferAttribute(sparklePositions, 3));
    const sparkles = new THREE.Points(sparkleGeo, sparkleMat);
    particleGroup.add(sparkles);
    
    scene.add(particleGroup);
    
    // stars background
    const starsGeo = new THREE.BufferGeometry();
    const starsCount = 2000;
    const starPos = new Float32Array(starsCount * 3);
    for (let i=0; i<starsCount*3; i+=3) {
      starPos[i] = (Math.random() - 0.5) * 300;
      starPos[i+1] = (Math.random() - 0.5) * 300;
      starPos[i+2] = (Math.random() - 0.5) * 300 - 80;
    }
    starsGeo.setAttribute('position', new THREE.BufferAttribute(starPos, 3));
    const starsMat = new THREE.PointsMaterial({ color: 0xffeedd, size: 0.25, transparent: true, opacity: 0.8, blending: THREE.AdditiveBlending });
    const stars = new THREE.Points(starsGeo, starsMat);
    scene.add(stars);
    
    // ---------- MOTION ANIMATION: grass sways, hearts float, trees breathe, stars rotate ----------
    let clock = new THREE.Clock();
    
    function animate() {
      const delta = clock.getDelta();
      const elapsedTime = performance.now() * 0.001; // seconds
      
      controls.update();
      
      // grass sway
      grassField.children.forEach((child, idx) => {
        if (child.isGroup) {
          child.rotation.x += Math.sin(elapsedTime * 3 + child.userData.offset) * 0.002;
          child.rotation.z += Math.cos(elapsedTime * 2.2 + child.userData.offset) * 0.0015;
        }
      });
      
      // floating hearts bob
      particleGroup.children.forEach((child, i) => {
        if (child.userData && child.userData.baseY !== undefined) {
          child.position.y = child.userData.baseY + Math.sin(elapsedTime * child.userData.speed + child.userData.offset) * 0.6;
        }
      });
      
      // trees subtle sway
      gardenGroup.children.forEach((child, i) => {
        if (child.isGroup && child.children.length > 3 && child.children[0].geometry.type === 'CylinderGeometry') {
          // it's a tree
          child.rotation.z = Math.sin(elapsedTime * 0.6 + i) * 0.03;
          child.rotation.x = Math.cos(elapsedTime * 0.5 + i) * 0.02;
        }
      });
      
      // sunflowers nod gently
      gardenGroup.children.forEach((child, i) => {
        if (child.isGroup && child.children.length > 8 && child.children[0].material && child.children[0].material.color.getHex() === 0x6b8e23) {
          child.rotation.z = Math.sin(elapsedTime * 0.8 + i) * 0.04;
          child.rotation.x = Math.cos(elapsedTime * 0.7 + i) * 0.03;
        }
      });
      
      // sparkle rotation
      sparkles.rotation.y += 0.0002;
      stars.rotation.y += 0.0001;
      
      renderer.render(scene, camera);
      requestAnimationFrame(animate);
    }
    
    animate();
    
    // ---------- RESIZE HANDLER ----------
    window.addEventListener('resize', () => {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    });
  </script>
</body>
</html>
