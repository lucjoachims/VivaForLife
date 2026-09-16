<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Le Grand Repas · Soirée DJ caritative</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;600;800&family=Hanken+Grotesk:ital,wght@0,400;0,500;0,700;1,400&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
  /* ====================  TOKENS  ==================== */
  :root{
    --night:#2B1A3D;       /* fond nuit prune */
    --night-2:#22142F;     /* fond plus profond */
    --paper:#FBF3E4;       /* talon de billet crème */
    --ink:#1A1226;         /* encre */
    --coral:#FF5A4D;       /* Repas + Soirée */
    --gold:#F5B92E;        /* À emporter */
    --mint:#3DD6C4;        /* Soirée seule (DJ) */
    --muted:#8E7FA6;       /* texte discret sur nuit */
    --line:rgba(251,243,228,.16);
    --ok:#3DD6C4; --warn:#F5B92E; --bad:#FF5A4D;
    --r:18px;
  }
  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{
    margin:0; background:var(--night); color:var(--paper);
    font-family:"Hanken Grotesk",system-ui,sans-serif; font-size:17px; line-height:1.55;
    -webkit-font-smoothing:antialiased; overflow-x:hidden;
  }
  h1,h2,h3{font-family:"Unbounded",sans-serif; font-weight:800; line-height:1.05; margin:0}
  a{color:inherit}
  .mono{font-family:"Space Mono",monospace}
  .wrap{max-width:1080px; margin:0 auto; padding:0 22px}
  .eyebrow{font-family:"Space Mono",monospace; font-size:.72rem; letter-spacing:.22em; text-transform:uppercase; color:var(--muted)}
  button{font-family:inherit; cursor:pointer; border:none}
  .btn{
    display:inline-flex; align-items:center; gap:.5em; justify-content:center;
    background:var(--coral); color:#2B0F0C; font-weight:700; font-size:1rem;
    padding:.85em 1.4em; border-radius:999px; transition:transform .12s ease, filter .15s ease;
  }
  .btn:hover{transform:translateY(-2px); filter:brightness(1.05)}
  .btn:active{transform:translateY(0)}
  .btn.ghost{background:transparent; color:var(--paper); box-shadow:inset 0 0 0 1.5px var(--line)}
  .btn:disabled{opacity:.45; cursor:not-allowed; transform:none}
  :focus-visible{outline:3px solid var(--mint); outline-offset:2px; border-radius:6px}

  /* ====================  HERO  ==================== */
  header.hero{position:relative; padding:30px 0 70px; overflow:hidden}
  .topbar{display:flex; justify-content:space-between; align-items:center; gap:14px}
  .logo{font-family:"Unbounded"; font-weight:800; font-size:1.05rem; letter-spacing:-.02em}
  .logo .dot{color:var(--coral)}
  .hero-grid{display:grid; grid-template-columns:1.15fr .85fr; gap:40px; align-items:center; margin-top:54px}
  .hero-emblem{display:flex; align-items:center; justify-content:center; position:relative}
  .hero-emblem::before{content:""; position:absolute; width:82%; padding-bottom:82%; border-radius:50%; z-index:0;
    background:radial-gradient(circle, rgba(245,185,46,.22), rgba(255,90,77,.13) 45%, rgba(43,26,61,0) 70%); filter:blur(12px)}
  .hero-emblem img{position:relative; z-index:1; width:100%; max-width:430px; height:auto;
    filter:drop-shadow(0 12px 32px rgba(0,0,0,.45)); animation:emblemFloat 6s ease-in-out infinite}
  @keyframes emblemFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}
  @media (prefers-reduced-motion: reduce){ .hero-emblem img{animation:none} }
  .hero h1{font-size:clamp(2.6rem,6.5vw,5rem); letter-spacing:-.02em}
  .hero h1 .li{color:var(--coral)}
  .hero h1 .ne{color:var(--mint)}
  .hero p.lead{font-size:1.15rem; color:#E7DCF2; margin:22px 0 0; max-width:42ch}
  .meta-row{display:flex; flex-wrap:wrap; gap:10px; margin-top:26px}
  .chip{display:inline-flex; align-items:center; gap:.5em; background:rgba(251,243,228,.07);
    border:1px solid var(--line); padding:.5em .9em; border-radius:999px; font-size:.92rem}
  .hero-cta{display:flex; gap:12px; flex-wrap:wrap; margin-top:30px}

  /* poster ticket in hero */
  .poster{position:relative; background:var(--paper); color:var(--ink); border-radius:var(--r);
    padding:26px 26px 22px; box-shadow:0 30px 60px -28px rgba(0,0,0,.65); transform:rotate(-2deg)}
  .poster::before,.poster::after{content:""; position:absolute; width:26px; height:26px;
    background:var(--night); border-radius:50%; top:50%; transform:translateY(-50%)}
  .poster::before{left:-13px} .poster::after{right:-13px}
  .poster .pl{font-family:"Space Mono"; font-size:.68rem; letter-spacing:.18em; text-transform:uppercase; color:#9A8C6E}
  .poster .ph{font-family:"Unbounded"; font-weight:800; font-size:1.7rem; line-height:1; margin:6px 0 14px}
  .poster .row{display:flex; justify-content:space-between; padding:9px 0; border-top:1.5px dashed #D8C9A8; font-size:.9rem}
  .poster .big{font-size:2.1rem; font-family:"Unbounded"; font-weight:800; letter-spacing:-.02em}
  .barcode{height:46px; margin-top:14px; background:repeating-linear-gradient(90deg,var(--ink) 0 2px,transparent 2px 4px,var(--ink) 4px 5px,transparent 5px 9px); border-radius:3px; opacity:.85}

  /* floating notes */
  .floaters{position:absolute; inset:0; pointer-events:none; z-index:0}
  .floaters span{position:absolute; font-size:1.4rem; opacity:.5; animation:bob 7s ease-in-out infinite}
  @keyframes bob{0%,100%{transform:translateY(0) rotate(-8deg)}50%{transform:translateY(-16px) rotate(8deg)}}

  section{position:relative; z-index:1}
  .band{background:var(--night-2); border-top:1px solid var(--line); border-bottom:1px solid var(--line)}

  /* ====================  PROJET  ==================== */
  .cause{padding:64px 0}
  .cause-grid{display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:start}
  .cause h2{font-size:clamp(1.8rem,4vw,2.6rem); margin-bottom:18px}
  .cause p{color:#E7DCF2; margin:0 0 14px}
  .progress-card{background:rgba(251,243,228,.06); border:1px solid var(--line); border-radius:var(--r); padding:24px}
  .logo-card{background:var(--paper); border-color:rgba(0,0,0,.06); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:16px; text-align:center; padding:32px 24px}
  .logo-card img{width:100%; max-width:340px; height:auto; display:block}
  .logo-card .logo-cap{font-family:"Space Mono",monospace; font-size:.74rem; letter-spacing:.16em; text-transform:uppercase; color:#7a6a50}
  .goal-num{font-family:"Unbounded"; font-weight:800; font-size:2.4rem; letter-spacing:-.02em}
  .goal-bar{height:14px; background:rgba(251,243,228,.12); border-radius:999px; overflow:hidden; margin:14px 0 8px}
  .goal-fill{height:100%; width:0; background:linear-gradient(90deg,var(--coral),var(--gold)); transition:width .8s ease}

  /* ====================  FORMULES  ==================== */
  .formulas{padding:64px 0}
  .sec-head{display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap; margin-bottom:34px}
  .sec-head h2{font-size:clamp(1.8rem,4vw,2.6rem)}
  .cards{display:grid; grid-template-columns:repeat(3,1fr); gap:20px}
  .ticket{position:relative; background:var(--paper); color:var(--ink); border-radius:var(--r); padding:24px 22px 22px;
    display:flex; flex-direction:column; min-height:330px}
  .ticket .stub{position:absolute; top:0; bottom:0; left:64%; width:0; border-left:2px dashed #D8C9A8}
  .ticket .notch{position:absolute; width:22px; height:22px; background:var(--night); border-radius:50%}
  .ticket .notch.t{top:-11px; left:calc(64% - 11px)} .ticket .notch.b{bottom:-11px; left:calc(64% - 11px)}
  .ticket .kind{font-family:"Space Mono"; font-size:.7rem; letter-spacing:.18em; text-transform:uppercase}
  .ticket h3{font-size:1.35rem; margin:8px 0 4px; letter-spacing:-.01em}
  .ticket .price{font-family:"Unbounded"; font-weight:800; font-size:2.3rem; letter-spacing:-.02em; margin:6px 0 2px}
  .ticket .price small{font-family:"Hanken Grotesk"; font-weight:500; font-size:.9rem; color:#7A6E55}
  .ticket ul{list-style:none; padding:0; margin:14px 0 0; font-size:.93rem}
  .ticket li{padding:5px 0 5px 22px; position:relative; color:#3A2F1E}
  .ticket li::before{content:"✓"; position:absolute; left:0; font-weight:700}
  .ticket .avail{margin-top:auto; padding-top:16px}
  .availbar{height:9px; border-radius:999px; background:#E6D8BB; overflow:hidden}
  .availbar > i{display:block; height:100%; border-radius:999px}
  .availtxt{font-size:.82rem; margin-top:7px; display:flex; justify-content:space-between; font-family:"Space Mono"}
  .ticket.t-coral{box-shadow:0 0 0 3px var(--coral) inset, 0 22px 40px -26px rgba(0,0,0,.6)}
  .ticket.t-mint{box-shadow:0 0 0 3px var(--mint) inset, 0 22px 40px -26px rgba(0,0,0,.6)}
  .ticket.t-gold{box-shadow:0 0 0 3px var(--gold) inset, 0 22px 40px -26px rgba(0,0,0,.6)}
  .t-coral .kind,.t-coral li::before{color:var(--coral)} .t-coral .availbar>i{background:var(--coral)}
  .t-mint .kind,.t-mint li::before{color:#16998a} .t-mint .availbar>i{background:var(--mint)}
  .t-gold .kind,.t-gold li::before{color:#b8860b} .t-gold .availbar>i{background:var(--gold)}
  .soldout-tag{position:absolute; top:18px; right:-6px; background:var(--ink); color:var(--paper);
    font-family:"Space Mono"; font-size:.7rem; letter-spacing:.1em; padding:.35em .7em; border-radius:4px; transform:rotate(6deg)}

  /* ====================  RÉSERVATION  ==================== */
  .booking{padding:64px 0}
  .book-grid{display:grid; grid-template-columns:1.1fr .9fr; gap:30px; align-items:start}
  .form-card{background:rgba(251,243,228,.06); border:1px solid var(--line); border-radius:var(--r); padding:26px}
  .field{margin-bottom:16px}
  .field label{display:block; font-size:.85rem; margin-bottom:6px; color:#D7CBE6; font-weight:500}
  .field input,.field textarea,.field select{
    width:100%; background:var(--night-2); border:1px solid var(--line); color:var(--paper);
    padding:.75em .85em; border-radius:12px; font-family:inherit; font-size:1rem}
  .field input::placeholder,.field textarea::placeholder{color:#6E6086}
  .qty-list{display:flex; flex-direction:column; gap:12px}
  .qty-row{display:flex; align-items:center; gap:12px; background:var(--night-2); border:1px solid var(--line);
    border-radius:14px; padding:12px 14px}
  .qty-row .dot{width:12px; height:12px; border-radius:50%; flex:none}
  .qty-row .info{flex:1; min-width:0}
  .qty-row .info b{display:block; font-size:.98rem; font-family:"Hanken Grotesk"; font-weight:700}
  .qty-row .info span{font-size:.8rem; color:var(--muted)}
  .stepper{display:flex; align-items:center; gap:0; flex:none}
  .stepper button{width:34px; height:34px; border-radius:10px; background:rgba(251,243,228,.1); color:var(--paper); font-size:1.2rem; font-weight:700; line-height:1}
  .stepper button:disabled{opacity:.3}
  .stepper input{width:42px; text-align:center; background:transparent; border:none; color:var(--paper); font-family:"Space Mono"; font-size:1.1rem; -moz-appearance:textfield}
  .stepper input::-webkit-outer-spin-button,.stepper input::-webkit-inner-spin-button{-webkit-appearance:none}
  .summary{background:var(--paper); color:var(--ink); border-radius:var(--r); padding:24px; position:sticky; top:18px}
  .summary h3{font-size:1.2rem; margin-bottom:14px}
  .sumrow{display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #D8C9A8; font-size:.95rem}
  .sumrow.total{border:none; padding-top:14px; font-family:"Unbounded"; font-weight:800; font-size:1.5rem}
  .hint{font-size:.82rem; color:#7A6E55; margin-top:12px; line-height:1.4}
  .err{color:var(--coral); font-size:.85rem; margin-top:6px; min-height:0}

  /* ticket result */
  .result{background:var(--paper); color:var(--ink); border-radius:var(--r); padding:30px; text-align:center}
  .result .check{width:64px;height:64px;border-radius:50%;background:var(--mint);display:grid;place-items:center;margin:0 auto 14px;font-size:2rem;color:#0c3a35}
  .pay-box{background:var(--night); color:var(--paper); border-radius:14px; padding:18px; margin:18px 0; text-align:left}
  .pay-box .l{font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; font-family:"Space Mono"}
  .pay-box .v{font-family:"Space Mono"; font-size:1.05rem; font-weight:700; word-break:break-all}
  .copybtn{background:rgba(255,255,255,.12); color:var(--paper); font-size:.78rem; padding:.3em .6em; border-radius:7px; margin-left:8px}

  /* ====================  ADMIN  ==================== */
  .admin-link{position:fixed; bottom:14px; right:14px; z-index:40; font-size:.78rem; color:var(--muted);
    background:rgba(0,0,0,.35); padding:.5em .9em; border-radius:999px; border:1px solid var(--line); cursor:pointer}
  .modal{position:fixed; inset:0; background:rgba(15,8,24,.86); backdrop-filter:blur(4px); z-index:50;
    display:none; align-items:flex-start; justify-content:center; padding:24px; overflow:auto}
  .modal.open{display:flex}
  .panel{background:var(--night); border:1px solid var(--line); border-radius:20px; width:min(960px,100%); padding:26px; margin:auto}
  .panel-head{display:flex; justify-content:space-between; align-items:center; margin-bottom:20px}
  .stats{display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:22px}
  .stat{background:rgba(251,243,228,.06); border:1px solid var(--line); border-radius:14px; padding:14px}
  .stat .n{font-family:"Unbounded"; font-weight:800; font-size:1.7rem}
  .stat .k{font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; font-family:"Space Mono"}
  .gauges{display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:22px}
  .gauge{background:rgba(251,243,228,.06); border:1px solid var(--line); border-radius:14px; padding:14px}
  .gauge .top{display:flex; justify-content:space-between; font-size:.85rem; margin-bottom:8px}
  .gauge .bar{height:10px; background:rgba(251,243,228,.12); border-radius:999px; overflow:hidden}
  .gauge .bar > i{display:block; height:100%}
  table{width:100%; border-collapse:collapse; font-size:.88rem}
  th,td{text-align:left; padding:10px 8px; border-bottom:1px solid var(--line)}
  th{font-family:"Space Mono"; font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; color:var(--muted)}
  .pill{display:inline-block; font-size:.72rem; padding:.25em .6em; border-radius:999px; font-weight:700}
  .pill.paid{background:rgba(61,214,196,.18); color:var(--mint)}
  .pill.pending{background:rgba(245,185,46,.18); color:var(--gold)}
  .pill.cancel{background:rgba(255,90,77,.18); color:var(--coral)}
  .tbtns button{font-size:.74rem; padding:.35em .6em; border-radius:8px; margin-right:4px; background:rgba(251,243,228,.1); color:var(--paper)}
  .toolbar{display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px}
  .toolbar input,.toolbar select{background:var(--night-2); border:1px solid var(--line); color:var(--paper); padding:.5em .7em; border-radius:10px; font-family:inherit}
  .tabs{display:flex; gap:6px; margin-bottom:18px; flex-wrap:wrap}
  .tabs button{padding:.5em 1em; border-radius:999px; background:rgba(251,243,228,.07); color:var(--paper); font-size:.85rem}
  .tabs button.on{background:var(--paper); color:var(--ink); font-weight:700}
  .table-scroll{overflow:auto; max-height:46vh; border:1px solid var(--line); border-radius:12px}
  .prep-list{columns:2; column-gap:24px; font-size:.92rem}
  .prep-list li{break-inside:avoid; margin-bottom:6px}
  .cfg-grid{display:grid; grid-template-columns:1fr 1fr; gap:14px}
  .cfg-grid .field{margin-bottom:0}

  footer{padding:50px 0; text-align:center; color:var(--muted); font-size:.85rem; border-top:1px solid var(--line)}

  /* ====================  RESPONSIVE  ==================== */
  @media (max-width:880px){
    .hero-grid,.cause-grid,.book-grid{grid-template-columns:1fr}
    .poster{transform:rotate(-1deg); max-width:360px}
    .cards{grid-template-columns:1fr}
    .stats{grid-template-columns:1fr 1fr} .gauges,.cfg-grid{grid-template-columns:1fr}
    .summary{position:static} .prep-list{columns:1}
  }
  @media (prefers-reduced-motion:reduce){
    *{animation:none!important; transition:none!important; scroll-behavior:auto!important}
  }
</style>
</head>
<body>
<div id="statusBanner"></div>

<!-- =========================================================
     HERO
========================================================== -->
<header class="hero">
  <div class="floaters" id="floaters"></div>
  <div class="wrap">
    <div class="topbar">
      <div class="logo" id="brandLogo">Le Grand Repas<span class="dot">.</span></div>
      <a href="#formules" class="btn ghost" style="font-size:.85rem; padding:.6em 1.1em">Réserver</a>
    </div>

    <div class="hero-grid">
      <div>
        <div class="eyebrow">Repas solidaire · Soirée DJ · Édition 2026</div>
        <h1 id="heroTitle">On mange,<br>on <span class="li">danse</span>,<br>on <span class="ne">aide</span>.</h1>
        <p class="lead" id="heroLead">Une soirée pour soutenir notre cause, autour d'un bon repas et d'un DJ jusqu'au bout de la nuit. Place limitée — réserve et règle ton billet à l'avance pour bloquer ta place.</p>
        <div class="meta-row" id="heroMeta">
          <span class="chip">📅 <span id="mDate">Sam. 14 mars 2026</span></span>
          <span class="chip">🕖 <span id="mTime">Dès 19h00</span></span>
          <span class="chip">📍 <span id="mPlace">Salle communale, Juprelle</span></span>
        </div>
        <div class="hero-cta">
          <a href="#formules" class="btn">Choisir ma formule</a>
          <a href="#projet" class="btn ghost">Le projet</a>
        </div>
      </div>

      <div>
        <div class="hero-emblem">
          <img src="data:image/png;base64,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" alt="À table pour Viva for Life — Soirée Repas &amp; DJ Caritative">
        </div>
      </div>
    </div>
  </div>
</header>

<!-- =========================================================
     PROJET / CAUSE
========================================================== -->
<section class="band" id="projet">
  <div class="wrap cause">
    <div class="cause-grid">
      <div>
        <div class="eyebrow">Le projet</div>
        <h2 id="causeTitle">Pourquoi cette soirée ?</h2>
        <p id="causeText1">Chaque billet vendu finance directement notre projet. Le repas est préparé avec amour par notre cuisinier bénévole, et la soirée est animée par un DJ qui reverse sa prestation à la cause.</p>
        <p id="causeText2">Tout est sur réservation et paiement anticipé : ça nous permet de prévoir les bonnes quantités, de ne rien gaspiller, et de garantir à chacun sa place.</p>
      </div>
      <div class="progress-card logo-card">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAk8AAAEMCAMAAADqPaIDAAAB/lBMVEX+/v4wJoPnQxD+/v7IER3////ueEX71sBcTZuRhL3HwOHvfAAcHBv////////1qYHu6e7////pUxfwhlPylmj////////sZjH4xKZfUJ2ros/TJhrU0On85NVqW6S5sdjCZDiEdrVHOI2dk8Z3aazcNBfIEB1jVUtkY2LT0tKTaVDqXCfnQxDsZwfQWin2tJCampmraUY9MIji3/AxMC/jPBNJRkLeThvxiS/IEB3IEB3nQxDIEB3nQxDnQxDIEB3nQxDIEB19Y1LwghCCgoExLyxCPTnIEB3nQxC3trbsdDWqqannQxBycnJUVFOTSmTEw8OmU1pXM3prOnTKZEl9QW7acC/vfADuegLzkTZLL33KdFPvfQDvfQDykEfxqHNDQD6Nem6liXfYLRrMKSbbMxfPOC/SSDrVWEbeeGLed2HKnoXMr57aw7XvfQDvfQDvfQDwg1D4sWzvva0AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACZsTAjAAAAgHRSTlMA//7+/oT///////7/Qrz//yb///9n1v/////+/////////////o3//////47///////////////9F1yknZddmubn//////xBG////EP////////////+Xuf///yBo//////8g/4D////P/////xBA35///wAAAAAAAAAAAAAAAFtw9Y4AAClXSURBVHja7Z2Hd9s4todjMhLLqFCyuiXZkuUWx3Zc4kySjeNkEk/azE7b8nbf7uu9v/f/n/PEJgEgygVISpaNu+fszMi0LIEfb/nhAnjwQN6ev9598+bh3F68ebP7+vm3D7Rpk2Zp98VDhr148/pXeoC0ScD05qHI3rzWfkobxL7dfQizFxopbSL7OyhNoZd6rkdMG9teStEUOKld7aS00e3LPzxUsV2dnWuj2KuNh4qmidKWCHXXyjj5ROmopw21x8bDdLarx1DbzDldPSU1y13fnvv/9+YNLDPXtZ62KHMynqIVW1L+/vb57hsd9LTByrprw4DI3pwpGO2itM1j3QwnYZ32q12dRWnj2gfDMMLK7g2k6v/2Nd9JvdAx756nToaxE5AAjlUCorQWda9lAiOMdi9kMOAT9VoP673GaUM+TPF7EDRQ9xmnHaWs51dvdFaujYKToZpEv9ZAaUvitKOcQv/qBTTk1co1Pdz3AycjTb7DyaJm1WK5WWmZptkq6xG/80JB6J7eqL/Jc75sUC5UzJlpoO4BTkZKwYgd8/6l0DIxa+lBv7v20pi5p3S587cUoP7+l0bHTJoe9Vk6Gdgd+kZfriKeNlLr2YQU9U//Wh0QHLnO0NYBb05TxaRbJbbCylUv1xFOT7Mo7WdA/XPj/4gh8noje803zdMMJxNgzZUs7YJwl0GLyRSof/ylaiXd0trM/BcKGqapFSA8mSvloeLkyQ93L9K/XbPSIULceeyWZuZqniJrgXhapbGaJU9+dfc66wFyt4fjtYT5PFU0TFPzx2jquG2727W7dmD+vzhz81ZsrGbRzg93qbtLEP/tbZJuaWaO5gnhiTVKK+nL59FONdxh0ydct4TxpAWoO8nTPNoZCtVdJHlXYqLK/n85/AGa2lALUHeUp1cGytNzKbeESt4hULWCeHx8665c0ZInT927w9MXBKdp+gR2S+GsLlaBzGfnxDxpAeqO+qfHhmz6VMNmdanmaJ7uKU+oe5rytCvtlnydsjuR58nTAtRd5Al1TwL1ieKW+r2JPaMDRUzMkxY07yJPmHtip+O1JtlsYnouWsT1pHna1ALUHeQJLe78dJzWN95MuCUvdkt4+Y+YmCctaN5Fnq4MUXlX483q4umQNE9agFqIXjBNVCqFQmEBLVYfDENU3rUozSYUIwJeV8iTrXmaP6+5+ieigaFVCehq5kHXNcFTsnO8zHdLrIBnw3jSgmY4wPzH7zwVT2W+tBPS5Tuv9DcDz8apckEA96gLq/9lBIMzLUDNb7ctlOrUeaqYEtaqpLklr8Q8Nf2/MhbjRAQ8LWjeGp6C6DK1cyBSKSLrlUHIBbv08D4C8DSRFQzOtQA142ktZ55iXm3bHjqOsznFiw2U8kP+0hDzFLhLD8DT2Z0XNGvlaZ5RqWS8SgDIUzkTnnAbT/Ga0rVN0FV4UPMLwqZ0QvUqwRNFHm8C53cDgVJGMOitkgBVa1ayfJJvB09ky0fXngRts000W/dTdaXqjiWP+2+7LR3w7pSgyVzRtAI8lcE8xU6hRa0CAVwZIJ4KwIC3JikYjFZGgCrkuYxpITxBcVpzTZG6wObqJYynctQwLxfw7LsjaM6fV8+yrE41sowaJG4nT9yEPdAVkly9gvEUBLyebMBz7oygGeHUabTx0bKWy1OzRa4iRiqFsipPffT22fZoWgp6Iq7ixP0ayJNSwLszAlSYOnXa5GAtmSfBKtAKfiGYp/j3sXgk5irYfOkKyFMgQU0k4PZtE/bZb/066rDWKRrG7eKpDFYlCzD1huCJmq6MuxOnx+CqlkzHme1PLRgfYYYNF6D6qyBABV+mbdw2nsSL1NPxdMa7aGz7XPWJzZc+gHkqCP9C9HfkBAOBoFkrN331sLl899Qwbh1PlWgSBbM+bcMDKZ7G8LvnS9j20NmO/2oyHWeuvgPPufSzEDR9jtDO4oVrVNFuTLXYNXvG7eSJX+mUkSt7UJ66cR/3moyFlfpjME/BqPYlA55YMCAFzXJ5ylFrEVtBRMT48yeBRWURJadlu6eseGrmxFMNudKRIkMqQCrxBG0yGKsJmkFg4xQPCxInKX+4zMyeMuNJXH/ZSjw9UOEp9k/bUjyFrbbXcJ4eABWANaQnYgTEmnODPXezl070nPuhZo0UJyG7eAU8GSvNU0uGJweu9iQCTZKnDebOvv7HPwe887a8oEnDqe86jt1ljSWBDOJGZ6GrxaqgmzI4mYWmJE9xIK3J8RSoe34jiZ09T1CxB+NpKMVTuPeSIcETtMnAlhegcIfUc4Y2JXeooekOme1EW3HUIJJMhfSAoUUbLU26weZLdvxoVPybPYDxVMNXurZmHpHGG2Jh/TWKSpkecKsHOZ5sWZ5saZ4KUjyBmww8GQEKDWzOyB6zImilAtFaTIgk49/ynm0DxiuYPfJvtkUOE7KRYyEx+4HlX01g6uZx3TptolOOpy6UjLjJdizFUyhM03h6KPTK4IDnwbg2BZHUBcUlUSTrB28z9W1wZx54hQqFp2KyAYqdAqI9d7DULWOealI8uTLyExFDpHiCNhkMZQVN0aU90E2I+0jdqaMLLdo6UK3yxLwnyVOVlMbKLdDEhzB18wZ03TgVT0rtBZ4UTpE8keSJsT5YqsnAk7htM2c2Fkf0uIvCt+0QmWGw1SSqeW1DEjsoT+dAnmbOyaquR/N89eL6PCi2aujEMssGjXro+Wy6p1woTyryE52n59yy9gwegaGCpujSiahatEFNDbaUco+1c3F48qezI0y8KQ+41RsW1nXnX9gp+naYDJ+N2b/a9M9OdKSYQp7C2qXclOLJZFcFIvmJwtNTDk/QJoOhhAA1AVxqCwU2EE8TeZ4cAE+FKCkarFNLwKKFAOUzUE1e47/doG2IeAJlWoQqKfuNZ2OpIj9ReOJunwltMpAVoASXngk9sAfhCQmbnsQwDabepM3RCyKJ4KTOuMZYnwPF5mlQN3g8dWV5IgFkTpVNpnmmjdbV0S9MpHhylXiCzrlswgUoG3KpEAIXosk7Up00s1+xDLbNVYMG56q6FReCbJ4ODSWewr3Kx2KeXPGj2A8rmRTyE40njgAFbjKYKAhQrrhnYQzR5G1ImZg5T22Da52oAmXw1DHXsVSqC453YHMBqQJmKvITnacX/D5qQJPBmURcgVzqilBxIDy5kCKQEu8APDUMAwBUhcWT1cFTc7h/yo8nFfmJxhN3f19owOtnK0BtS5RuIJ4cCZ5MMU9VEU5GPdCVyiyeDkU8pfZPjiRPSvJTsn1cdLoU8OEewQUDF7BdlCMqYUE8eXnx5Blia4crQRj5EyEddHkacbY82bL+jKt2XVMT8tf8lR6AJoNxdgKU3XW2PWHHICTuy8uZQJ6KAJ6Mk/hvU3hqC3lyhJN+GfMkJz+NuDztCgKeDQ94IzhPDsmRv0AHGtM9wDVSW+dJ8GRBcDLqHJ4MVZ6oXsTf4sLuAXnqqu8en5SfKP2Z/AIPPOeyDf5YQ+IbnNnsBTmQ3CjL6RYYTw0QTzMNFMCTnYon6i/YAJEXNTn5aZPL00NI6xdMVpIQoPr2fKEEw4YALQCkGK9lyVPdkHJQy+GpC7xO1oNj8hNlvZTo/Dtok8E5NK0bZ1GkIMOS5fQdiKcONVdqsESoW+afHHn5aTy0zyjpBosnwQFTLVjA60kLUBztZLMv8nZDqRH2pHhizLf4s7odarhr071WAyQu3BKeRBGl724jbbSzPjBDNiGHNhkMwZ7A43TAbTth+5Ij4sCWGmFwMTxkzAfHN55e3VXpL7eXyRNAlIPKTw55jyYTLk/8hBzaZACW7ZPf6DxoIT+j6Flppm1UeIph5/HEyLwbrN9YCZ5cKE9Ew/W1rEIeBjzg2SygvM4FtJDbwrpMPBCuSjE8EfLkMRKlKkutWgmeelIOYN6nSivwnvIPpAbOuUygdacDuMu2sIp1hQWlEk9rQ0/Ak8Xg5nbx5AlnxuDyU9CbkljGXaPvJyY+sgzaZAC9dQ7kmRB+zU3hFX2ZdabEx+PwdALnqcr6BfJtu5nzJDsdPBL3EmDbA8RHR7+kJ1C7ot21AE0GfaBuP4TEbA86fk6W0y2zj8fhqSrLk7UKPNniXgLYfqxhAvVCuHsDcGsCQKY1K80AS6Y84Qx05jyNl8CTfZt54u9ReS2fQAGbDGyg1DOGaB49qFrJ5OlMLNZ1mSPO4Wl9JfxTT3I6GNBLQLfH8gkUtMngHChAQTRZYRfqWDRPAJDHHaZr5PBUhPNUXJ5/crLjiX9o4Qd5BQq6Pfo2UIDy4EumOHmi6D0APG0zXePSeerlxFMwMrY9CQ906UPlJ/b+btSE/ClnUSe8yWAo21E3EidZPRGVtohIdjFhu8zxWwhPh42qZVn0L+HmyBPltzZFcgGbjSuFgAdsMvBgAtQm5EtD5cquujzeXSpPRYvnP6VlRwfamSFZJQu3PH2sGvCGMFAkBCjxkilOYN8W+CdHuI3Q0FwiT1X+knlXveFCpqJ15OQnmr1SCXiwhZ0TWFx0JJZMsdMxR8BbT3gXnCXy1BA4HWmeeko89dLKT4wEChLwxE0GZ5ICFGDJFPubDgU8uQCe7GXxFPXa9TdZ+9NJ8+Qq8eSmlZ9YCZQg4AGbDPogYcGGVKnCBmIbztOE9XAujafAPfXOZOdg4TytyfGkLD+xEiiRpAlb2DmSFKDsNJWHYCTOhQ+rS08bFsFTRzSci+XJE91THhofDPmmOmCTgQ0ToMAqP3fUBQNnAnhylsWTJSpcFsQTUH5qcXfHNQz5Jihgk4EHCt3nAMGgKxwZlz8S4sFl1A6L4Mm8XTxtAtQd9JQyWMATzrmIJageKM1yJQQoNp58nmwIT+4yebJz5MmV48mR+CStxOk69IC3kUWTwRAkQG1CvrZQmXPS83SueRLKT5R2f+Lwii+sgPc8fZOBnADVB4yok5onl33FMnnqLp8nG6Au0BZJFsQ9K8KMvADSAjYh32UECfOboqGxuT8dgXg6u9/+yYaqVdgpZYl67xUj4HElA9icywiSZXXBbRK8pK3L9ZiOcNJryBhIRZ5OJHhq582TI8fTGtTOoo0SIBWeyEGBJlPGkE9nQwSDkZA5cTQUyOP054O1RljAkyXBU/GW8DSS5Ymhbz5WcVCwJoO+lAA1BEBnK/HkgnhylshTFzRCufLkKOz9RNM36RXeU76Dgs25bEOW4EG+91i484cL5GnI1ja2M+Gpeut4GuXHE7W97krFQYGaDGzoPB90yRQ7RYLyZLOvcO8oT8DdUralvNm8TqqAJE1fMhDuZACQyMUfz5VYMsW+xgFuE9llf4j+Enmyc+RJrr1Aiidqex29aUXgoGBzLpsAAaon07Fi8niy1adbPMbP7h9PUluPe9R2zWuWg3qTtslgAgjHDkQw6ImusTlDcQaRx+8fT655Hp4kObJt+0yJJ3p73SuWZMATyUFNBmNoXwt0yRTz69qCn0F46q4WTxMgT3IyhLRcUAZ21fkO6oVAghIG235GAtQQspW2LZRMWe7yjFX73Wae7JQ89bPhCdpkEGRQr1M2GTgyPI0AF/UUeBqCpluorK4mTx6IkPRbj7Pa6xgZ+dRBcVYmgJoMbEA8Bglvwu/rgrb12+Z5sO27whO+QVt+PDFXd7Id1G7KJgNvCOcJsmSKlw2cKU+3dFljeZt56sJ+QW43jPTyk8BBPU8nQfUyEqD6oqR9m/1ZeqDpFmq5KuCpAedpfVn+KUee2Ks7r5kO6kW6JoPJNpwnE3LRkO2E1OVx5vo9AU9VOE/V2+afbOm9n4DyE3sSz5/F40Q8UJOBOCBvy3SsMB+gYQY8jekZ5wJ46ubnn+R265Hayp6zuvOK6aDYKjmoyUCiQxOyZIqZZNlsnsSrpXqsn9qrz5Pcbj1ZyE8cTdMXNV98m6bJQByQJ5CHwxa1BY9BhTHfg03oiTqbpw6cpw6Lp7oozJzlw1M3NU9d3mq8K3ZKvpumycCGfSzoJr88alLz5Mj6JwvOk6Xc72uny58c4bxE1vIT10FtcGq8AmTOxQa5TVOkPggfS68vvh99Pk+bsjwNGDxR4qCXOU9A/zQUqyhZy088B+Wn5KwUCrp7NFSAgnSsMAOsCyhkXH6G5cryZDLSoSpr04sl8CR1uFQ28hPXQe1wUijgEdVQniBLpphfOQ1PzHAo4qlNX16wzpAzldZLpeTJzo0nweZiV5yIt5vuiOoMBCjhuk/HFScKTtY8NeiJUvJ8qZOl+SdbNKBQuWA69K4TndEkkJ+4DiqIeK9THVEN5ekMIBh4sjw5cJ5sWZ4oBV69UT1MvjpQ56mbLh+HNSGIFRvkrMLwvLIzweZiTAe1wwYKtpMBWNCELJliQTcE8DQS3ICJLE+mAbOiuTT/JMWT9McoS2/eE0U8ek7elNbouTd8Avk6jD/XdcWOnfGrZ25gPacrw1OFsaO9YbDUp5XmiX7YMG+RJmsWL1A1k0D91+9+9/v//iqwrV9+/PXPyjwNpZZMsZzMeFM8cF3Zj8bjqcAIeBz3lGm8S8nTOeUt+6KnnjhXirc3K7vNIEqhUKD+80+//yphWz/+pMSTDdgCGRmkbUkl3hWetaHIU4Fe4TG2DAv4W7x/kmov4Ak2vUgdqJWb8Xll/K3F2H1QUQo1A+ovf/cVy1SQGssJUMw6TlkeV+SpBjjxYL5DZqGwFP8kxdO2jDpQrj0Q2ReDm0KFSfmMpm9CI73Ub9QFKNCSKU9SiE/DE2/+rhDODzSEOIVnB9ey9k9r2fPkKDWnqDioOVB/8qE5OqpP7fCw/enTp6+nhlH1W1miPKklU6yoJebJzdY/hfMDwohX98JMI2v/BJtA3pSZZR6qNacoaAZRTv7wze+/OpqpdfXDdvuTf3R8cd1nStlHuVIdK8wqTVkeV+UpdFACoOqB9FR58GBh/slWblcRyU/yPH0wBEBt7ODn2LTbU5pCQ4n65aeseRqqTQqgA7eZNU8PWqw2crK0e7ASPIk+xQN5uxYB9XCDeP6mQW9G1Ndzon6tIkCN0laBPA4lQYTw9CDqq6uznFN0NEstBU+jW8HTUJGnl5xnbSMiaidx2tbUS8VEzZzU1k/yPHHu+Jli2HJy5akWd2oecmgKj6hQ5clJxdNIgieR/NRS4ImTks+BShI1ReoT6aM+/UbahYCWTJmqPE3UeLKSNt+ItBYrfIMqHvbajVgUb9Xm3WLStX9anmTaC1zBpFhFhSdOSh7rUHSi6nHcm7uoH2VTHNCSKbkJnm3FxRvkJGjCamhXYSRlWFY1MAs50K6CdovRjTsFOglng7z8eZKSnzKJeMbTmYsiM/PAScVEyQE1BrkeF1TW8uRxhYlGl8nA3PmXWxzqEA25wrxopDLhCeNJpp3OETzNajxxIx7qomhItQkXtSUnaIKWTMl3fanKmdOsrS/EySeKhQp+fgDrqt5afjyNJXgSyU9NNZ6+XPEFuo2HHKQO2wpAeXIClFTdf56Kp2m46fnRJsCqVZgZObS1ZoUCEzkjUSsnrKVQJwh46kKi4qZkFqcmPwlFqMhH8ZCauqhGYx7ztqR8COCUKchW+lTfl6pRCzCetXKhUIms0AQOfiV7nmzVdpUzweDXFHkSRDwRUoefMKAAOdSm3JIpU40nNw1Pnnr+sDI85SA/ieaFsdwcY2qHAKoxi3k/AgcIvGRKJrEeZ8STq1wvL5WncwmecpGf+L3kFKZoRNXbAVB/EQH1GzhPoI4VGSHJVk97sxzQZfHkAtLKfOUn4bQLG6mNp/MkKgQqEjZFSnlXbsmUzA2wIeXw2Ams527m5PBvH0+SZWbgnsvqX/OlIWOzwLdBAPU1LCeHpUabKpFrBOHJBoqE5ZXgqavIk0h+Ckpc5smc6VNyqs75lAqUaHJYUoDyZMdeIK9ATllPIcAs0T9tS/A0Ad0ghKvMpl14OucOBtSfI6B+gvIEWjJlKvFkA+5ANw+BWHRYfI48ORITSV3IZDx4tZS0CMUiCgNq/c9hTv4LVNAELZmSKPA2IeM1AlzTy6fAM1V58lLxRGsv4KzWGE9zy3POHGYOEW+2DGaeRAVARTn5z0BBE7RkSmLCCzTdApnKcfJJyJV5cjPnSTz5RJzMKeewXxrytoEDFQibYcT7LdCLgJZMSaxxz4yn7srwNARMyVF4kph1CLjyZBUUloOq14+OQqdzdFSnxrwYqMPiemN9/Ruxg5IVoOAFngcZL8jU4Fk+BZ5KX5aAJweQEw1T7v2kosi9ZNJ0hKxmIZB6SgIVT7z8AuPpPONWAdB4gXDOZ8ZFpY9mG86TRPtTT+FTyCWU11ScAkORIojCkvL2vNfgD6kFqJ7CSl8QT5sQPdW9NTy56f2Tk3LvJ6Xx4Jd4U6hmSNUTHmpnlkKtF78WTuPBSjdHficCG5RzgRzfdi4FXs48rcF5ko26CgKKKP2eE4X6qKeosOlHvNBB/Ta1AKWwVAU03QLjaZJLQn57eJL9FAoCL0AyOKK5qACoeTtU5KB+AvE0gkzzgVvqpHmyc2oBumU8uVLyU1b1CajNoB67KMJDRTl5/dOUp29Eky59qVOm4Lt2guRx4JY+uRR4KtsIwXnyJHiS/BAqjxdMgoqjHgnUThzxiqGD+gVUusGWTJmZ8gRbGnCeR0Ker39y4TydS34IpYYLoIyZBGoHi3ghT59ApZu7ljIuJUtrGZ4cfhVYuTU8nTuYDW3b7k5t+o+eEk+5y08ybVCRizrClfKN2SqqMOD9AeRHYDwNJYZe+L4wFSaXljplnvgW7b/himXexclPMnN4EVB1POJFNV7706dvBBL5SLJjBVrgSfPkZuzhl8RTIeyE2YS3q+QvP0nNCSeAQh1Uuy1KyKUFKKB77oMyeJKnru3/zx6FoWTbRVbo3pp8nG81QWfVUuQniT7yGKgjXDSIHVT7E5ynLugqYEsdC0A7tEkITCCllGtN8X3KOt7VVP1Ti/spaw+keVqA/CTXBFUnHNQO6qBEPK2BHK8NKPAiUiLfEvj+cnBSX+Bjzpn3gL/DgEoHGdg/mS5um3iq7Uz8XNsO/i/4B+YakktEo3bcllw73ViBJ/nRMGSBIhzUbNUwnCfYkilzFDmXOBz1Ob6/CUo4+FugmEoPJKg/U8WaIFIn8HaVBchPsl2/R0kHFWtQQQIFEzTFHSsVqZGvzLb9EscxEqh4tW9oOSxHCE9hVrAaiKfeaOrOxnnwpFicyDVpJh1U3LhyJOLJBS+ZKgD8CEkKO5KFwMyfdjRiLMSaSjgVJD1fcPxDqFLReVqI/CTbRR5EPFyDQn/yM0x4pAb8aXTbDCusAsTf+CswMFKa8U4W5YUTI3RRBcwquLUUceI9dOdBNPD/kvLeoo6iuCvX73vECniGiCeWoGkPyVb48nywiHB060jJhT7sG07/i7sUS5AHhG/hI9uz5WtMlcmnx9IOih7wfJ54n29CVK627TjUHLvyQFvscyt8Z9WsVEClanzVtLAc2Xa+PL2UdlD4pMvGHLQtYGNJbzO5uZ/Sioo7TlMLNib+plPNMI626AUimcIR5yZmJz/JTOHNAx6Lpx/FPFU45XOwqVJNcxRFKX+kBifF4vp/yOlAIV34VmcMNxZydZZt884r6YCHdxnMX/8ZoI81aYm1nxppghLJthceHXP8MX0OUODKYOF5nFk1F16p8/R0xtMRv9+367gR8EhVorr1wt23YJPq2UFEbzOZVfTdVoWfwvtcTUI5S/W4BGkHdYTmTwbKE0N9sie9PirSlSs6sAEm/ax5Gf0202mgmpirc7fnppgbv86AJ3p1ZzuuzrWVcELPBX10kMdGQsi5iQLBIdcSj+RpI+aJcE9jxC1pnGRxwg7dy4cnMVfKk5kyGhS+BH0j4ukIy55sZ9NLJN1NHeFgljjDMV+ekAQL50r98f9yJdMGZVD802yx1HiynRApKwWdLcGtlTgSdG8xPGFcVdJsfigT8ep1nKedsOr7dVDEJdySz5JGRFIoIM9U3zPNVRtEeI1XJzbHCDo0v/rjMOmWWgUd4lg5S6HCGKOyaQ7IIS/lsMw0b5PfXCzSM4Ps6X+1W5IVK5HnDkWqlTxe9vj7XDYeztmu1XgK0qe/0m5JFqdBtVEsFqudMEEoxCPWNM0qOcin73LZGD1n+3KlwNNGEO462i1J6gGDNnIs4wAhqpWMdsajzyuptKgAFbinjnZLsu4JPzy23YnVnnKitpva5UEe+1jfxhxqx6/uqtotyVklecpwQFQl6ANIDPLxKqbjslVeFO6m7ul/tFtKz5NhFD3/qE/TPEn85PS7VUzHIx1KLuZNs6dXmg+FeEfTYcK0oZgMd59XeaJKJubt7Fy91HhIWzMpCQR24vNECXfeiqZPkYuCCwc7jzUcKkaLar5NM1EvWd19v7rhLrQPQKIef8niYS3cP2uZH7eoIzqgqE83n1d/hcZLSNR7lY3rv5+294zBExkKT1c83MVi1KurBTgnfy32wLp/5pkf39IG1SeNqP32vlv1cDdzUq+Yce/61ZdM/gS1nrkH1jbN708ZPOEjslX64S61IX54fE36qevHH75k9O7le4qTX8odlI5p8e6E6FZ5e7myYibbUX348Di0Dx8y1QfoQsx9sLpnPnmfBMoyrQ6WQW2VPur10hI8WfeUJ6M4jXiXiZy8Yw7a2Aze3uXqtdJpnpYT8byLS4oAVR8gg6Ldk+YJaoNpCvU26bXWT5Ak4PJ77Z40T8AUyjQ/JoAyzZP1eZFy/NcHq9hJp3lalmjwjgRqmo3XkQSqrRctap7A1kgCNX1pHZ1zaeeyRew94ck/Vyi2Q9oNEF6QWSwqsixvoEzTslAFqm3ePf1pUTw1kPmtKm38LeSC/ITQetjQTbW0/rRh1Ynw9q6EyQZV8q+0TZ1CKfJURG7cydYxTbOZWWeLckEmts6bxj1JJzpZ5IOSAKqeoLaqUyjVeIfcuIPSI657uiyVTnNxTh1uW8B6SpqmVk8ChSrl1YQX9O7KhPDC83GMpz2ee3pSKl3mgtOA32ZymJamRCTv+F/m/Sn2CT4+I9IAnZIr8TTg8oTe6otSaSsfkZFvGbi9QWKOxfxYupgTtHdgfr45xj+U1siVeEIDWsL/NHD3tJfPJAjfOinEy/ANGsnO8SlQPyD8vP88Bez9KX6BZkWFJzR3KZXYrsMrlUp5ZONFUVdlVfmtq/5evessiBtoP+aB754vkAbOv9I8qfFURW7d9yW2e3pXKr3Nwz1ZIp7URYp6g5V6HeL599tS+Mjsz13U32ie1HhCmdknPBCWW5Xep3JPxfUqTQ89FHZ913OAuINl+c/efxf9rc8XN6eBj3r7neZJjacixtMWG7XSo7kvqaKOZUCZJCNynzg1ttoGJ0Oj2gCVpzy8IGVkWsXOgB83D/Gs7LT00Zx+m7K/PdSTi9Leo7c30wCoBSglntpYyr1FyWcj9zQth7AYZDELsHlK1k7IlW3mtYxlElU0Jnfwz0tYmBEVB0IZq4F/jpuLYAavHB6P8MOT/f3vDvSMi+p8MMbTI0ZmFUiZ81u5t3eK1GV7ewyP93lv75jU2I+Zf52dKaF+kePQ2uSnZslYFuZTH5WemJ1i2PQ0P75Fy0+KPCG7b35EeULd08dS6Qa5lZ5PVxWBjZFhX4QKA7a/J56FtUGJN3pJhy9UdSAyFjaHc/x+6p4Oi3ETXc3fbrdS0N5JlSeLMeFSTUiZJwhex0gQw9P4IqpYHSdSJFzEWjeZmiMNujpP/bS46VgH4wltFy99nuJV1E2ZGfHUoQvk9aSUaSH/jXKDp/EW4sXeEm8UXMwQMzu8FpM5clyhqg2TsUzzj4hWsO9zqnnKiqcq3XkgL3vRTAvCxB7KCZbGzzH7LpzRqJI8HdOdY5VX3c+QK3LnjTswGWv6KeY4XQS/qXnKiic0RJTe0xKbSMosIjr6IxQvLI23kJLwUdI9Td9si56OF8UTfA1Ouh2k40AZyzPfhcLls73SxUGQTGmesuKpiOVJlCgYz7Q0EFJO0dv8GeGpgTii9xT3NIXvlJ6OG+J0vM3zQB7XeQ1wh3fgf4pnpxel771wPDRPWfF0SEtu2qSUiSL2OeTLoqXxs1ruhxC6hP6NObMGJB0vosi1ox7g+W/GXcFt9O06ZM9wm0jwn5QuLkuli8/xGWWap6x4MmgCeQefaXmGhZ13YVg8oaTxDUREuKHW71gN2ZFKxy3Rix1oT8L0tw/e7T/5OO/+1DxlxpOXTG7a2CRxSEAdeWUPz9jnPCExMHir5PQc1mQ1IMVtbjp+InrRA7d0xjgOiobmKWOerGRy0yGlTEo6jmhH0RWoe4qkzGS64zE0iTbz3lMp8Sgc1gFvNgt5DcuyTopoUNU8ZcNTJ5HcFGlN41UkAG6RgJF3+Ul4DU0PmsGH/x1hWxw2Y0JFB5s2lm/D0jxlwxNSgf0Q8mTRujI7yEvP8Hsap/FVQspE3ugzpYZE/7IFWPvCytETb9fRPC2RpwaZ3BSpTeOITnlDvBKl8XVUscIngp88oQnkHciiqCoNOeqLVoqWTs1TZjwVSZ4sTMrcIxWEi7hRczAnbIuMiISfu9inzc6AFkVRKbHo0yj81gPN00J4ahMrEorUpvF1rLkAv6lhGo/Gv2CmpYhguU+ZnTkE3XDGjEmSw3aaNTKap8x4MogVCYh7+m7eNH6CCJVbxEthGp9wTwMEwUuKQL4OueFt2owJ9UVOZ5SleVogTx5azaF3OezKTDijEpnEBGl8nZQyG9gEoJmcnalKqZmeIajkTtKskdE8ZceThc3+D5IzLUaiuQC/q0HahXZHnWKYer7CThHIQc0FJzTkqC9aaZasa56y46mDEvRvqHpQunmWiDBzxoooT4eIe9ojZ4bRtQxzgRzUXGDRBHTZdLyueVogT1VUbvLo+1+QzQW4PjBFpENImXViAtBKzM60QTecn44XQQtDPa0XLJInJJF98s5EEh1Eyu5gvb7kbS2V2uRMyxzSJ/v7f1ssWokmqwbkhlOFyzbtxUaqJeuap+x4Qp7szx5j/wukueAi+eJlqUNImXX23Y2XL2TQXDBgrbyiLaTSPC2IJ/oaXXz/C7K5AE9j9i/JmRZOH+V+OF2TRXNBh9bFqbRflOYpO56omazfNH5McWFIyTcvs/Z/mCtWQbMUT1uMBXJQOk7rI8AagLnTxpqnpfDkUe46vv9FlZKOowIUKWXydpx7QrYngJoL2owGYG6ipXlaCk8U5YbY/yLRXJAQuGPfcyNyT3FTTBVS3hUX0lygecqWpw4tKGFrz71EcwG1RI+lTO6GmB+TLmxA2ya6vbDmAs1TtjxVqe7pGS1jv0ST9HqydLsRb8ITHaHiQTYSo1JCzdFTbt+qecqQpwalpse28qU0F9AS+ahpXLBHWCSQCzd+KrIoGXCbCwY6H182T7S4dUN1YD/ge7ISmwlEUiaywcr+3A4IgRyykdghv7ngMKPmAs1Ttjy1Be6J1lxASeSj/S+wDVbm9ploN5faSGwgagCuptwPX/OUIU8GV8rEmwsuDVaLSCxlYhus7D2K7d+JbTpFPHVYfQRV7ouK++FrnrLkKRG38I0PKb2+lLsYzbRgbXSIJNogtsToCHhqSDUXVFPuh695ypIni5Qy9+j5+gERB9fxkvBRso2OvvHKliHeKLoo01zAU7x0Pr5wnk4IrYDY5/eE1lyQ3MyVWJbu4ZJokez47QiPRWjzmwvqHBcrux++5ilLntCn++CiRJ4LNGCk42gS9DlyXQ20+fwR9dpIIOef22Kx+gga9B002un2w9c8ZckT4qB8nG6eMaj5njxyo0oqDHNICEkUeZdo1Sj/XKkTVh/BCcPxtFPth695ypQnYz2Uqw+mOU9i43p6cwEK1MHUFxFr8MgZG8TLzTt+6TuFzzLwATcdJ5tc6tWB+n74mqdseTIeXe7v7wdC0Q3lHISbWEUiD8B7dvH9/v4F9pO9+Nr3z1jvksvBQqlM85QxT8ZpeLdvHj1L/ux4pkomWDu+DAmZ/eC0xGJvTtql5unO8zRFY4t1oOuzrdgYv/YM/0/Gtce8t9E83TWe7rNpnjRPmqdbwdNA06N5ysya+Zwot+rW0AdKqVktxYm8d9fqntnSbChZxTSt9aI21BqePqBM2VqmtqTp4zhTpOTaCGvd0mT8/wFM71PTczYlhQAAAABJRU5ErkJggg==" alt="Viva for Life — VivaCité">
        <div class="logo-cap">Au profit de Viva for Life</div>
      </div>
    </div>
  </div>
</section>

<!-- =========================================================
     FORMULES
========================================================== -->
<section class="formulas wrap" id="formules">
  <div class="sec-head">
    <div>
      <div class="eyebrow">Les formules</div>
      <h2>Choisis ton billet</h2>
    </div>
    <p style="max-width:34ch; color:var(--muted); margin:0">Les places du repas sont limitées (notre cuisinier n'a que deux mains 😅). La soirée peut accueillir un peu plus de monde.</p>
  </div>
  <div class="cards" id="formulaCards"><!-- injected --></div>
</section>

<!-- =========================================================
     RÉSERVATION
========================================================== -->
<section class="band">
  <div class="wrap booking" id="reserver">
    <div class="sec-head">
      <div>
        <div class="eyebrow">Réservation</div>
        <h2>Bloque ta place</h2>
      </div>
    </div>

    <div id="bookingArea">
      <div class="book-grid">
        <div class="form-card">
          <div class="field">
            <label for="fName">Ton nom</label>
            <input id="fName" type="text" placeholder="Prénom et nom" autocomplete="name">
          </div>
          <div class="cfg-grid" style="margin-bottom:16px">
            <div class="field" style="margin-bottom:0">
              <label for="fEmail">E-mail</label>
              <input id="fEmail" type="email" placeholder="toi@mail.be" autocomplete="email">
            </div>
            <div class="field" style="margin-bottom:0">
              <label for="fPhone">Téléphone</label>
              <input id="fPhone" type="tel" placeholder="04xx xx xx xx" autocomplete="tel">
            </div>
          </div>

          <label style="display:block; font-size:.85rem; margin-bottom:8px; color:#D7CBE6; font-weight:500">Tes billets</label>
          <div class="qty-list" id="qtyList"><!-- injected --></div>

          <div class="field" style="margin-top:16px">
            <label for="fNotes">Allergies, régime, heure d'enlèvement (emporter)…</label>
            <textarea id="fNotes" rows="2" placeholder="Ex. 2 végétariens · enlèvement vers 18h"></textarea>
          </div>
          <div class="err" id="formErr"></div>
        </div>

        <div class="summary">
          <h3>Ton récap</h3>
          <div id="sumRows"><div class="sumrow"><span>Aucun billet sélectionné</span><span>—</span></div></div>
          <div class="sumrow total"><span>Total</span><span id="sumTotal">0 €</span></div>
          <div id="payButtons" style="display:flex; flex-direction:column; gap:10px; margin-top:16px">
            <button class="btn" id="payCard" style="width:100%">💳 Payer par carte (immédiat)</button>
            <button class="btn ghost" id="payTransfer" style="width:100%; box-shadow:inset 0 0 0 1.5px #D8C9A8; color:var(--ink)">🏦 Réserver et payer par virement</button>
          </div>
          <p class="hint" id="payHint">Par carte : paiement sécurisé via Stripe, place confirmée tout de suite. Par virement : tu reçois l'IBAN et une communication structurée par e-mail, place confirmée à réception.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =========================================================
     ADMIN
========================================================== -->
<div class="admin-link" id="adminOpen">⚙ Espace organisateur</div>

<div class="modal" id="adminModal">
  <div class="panel" id="adminPanel">
    <!-- gate -->
    <div id="adminGate">
      <div class="panel-head">
        <h3>Espace organisateur</h3>
        <button class="btn ghost" data-close style="padding:.4em .9em">Fermer</button>
      </div>
      <p style="color:var(--muted)">Entre le code d'accès pour gérer les réservations, les paiements et les quantités.</p>
      <div class="field" style="max-width:260px">
        <input id="adminPass" type="password" placeholder="Code d'accès">
      </div>
      <button class="btn" id="adminLogin">Entrer</button>
      <div class="err" id="adminErr"></div>
    </div>

    <!-- dashboard -->
    <div id="adminDash" style="display:none">
      <div class="panel-head">
        <h3>Tableau de bord</h3>
        <div style="display:flex; gap:8px">
          <button class="btn ghost" id="refreshBtn" style="padding:.4em .9em">↻ Rafraîchir</button>
          <button class="btn ghost" data-close style="padding:.4em .9em">Fermer</button>
        </div>
      </div>

      <div class="tabs">
        <button class="on" data-tab="resa">Réservations</button>
        <button data-tab="cuisine">Cuisine</button>
        <button data-tab="reglages">Réglages</button>
      </div>

      <!-- TAB réservations -->
      <div data-pane="resa">
        <div class="stats" id="statBoxes"></div>
        <div class="gauges" id="gaugeBoxes"></div>
        <div class="toolbar">
          <input id="search" placeholder="🔎 Chercher un nom, e-mail, réf…" style="flex:1; min-width:180px">
          <select id="filterStatus">
            <option value="all">Tous statuts</option>
            <option value="pending">À payer</option>
            <option value="paid">Payé</option>
            <option value="cancelled">Annulé</option>
          </select>
          <button class="btn ghost" id="csvBtn" style="padding:.5em 1em">⬇ Export CSV</button>
        </div>
        <div class="table-scroll">
          <table>
            <thead><tr>
              <th>Réf</th><th>Nom</th><th>Contact</th><th>Billets</th><th>Total</th><th>Statut</th><th>Actions</th>
            </tr></thead>
            <tbody id="resaBody"></tbody>
          </table>
        </div>
      </div>

      <!-- TAB cuisine -->
      <div data-pane="cuisine" style="display:none">
        <div class="stats" id="kitchenStats"></div>
        <h3 style="font-size:1.1rem; margin:18px 0 10px">🍽️ À table (sur place)</h3>
        <ul class="prep-list" id="listDine"></ul>
        <h3 style="font-size:1.1rem; margin:18px 0 10px">🥡 À emporter</h3>
        <ul class="prep-list" id="listTake"></ul>
        <p class="hint" style="color:var(--muted)">Seules les réservations payées sont comptées comme confirmées pour la cuisine.</p>
      </div>

      <!-- TAB réglages -->
      <div data-pane="reglages" style="display:none">
        <p style="color:var(--muted); margin-top:0">Personnalise l'événement. Les modifications sont enregistrées et visibles par tout le monde.</p>
        <div class="cfg-grid">
          <div class="field"><label>Nom de l'événement</label><input data-cfg="eventName"></div>
          <div class="field"><label>Au profit de</label><input data-cfg="cause"></div>
          <div class="field"><label>Date</label><input data-cfg="date"></div>
          <div class="field"><label>Heure</label><input data-cfg="time"></div>
          <div class="field"><label>Lieu</label><input data-cfg="place"></div>
          <div class="field"><label>Objectif (€)</label><input data-cfg="goal" type="number"></div>
        </div>
        <hr style="border:none; border-top:1px solid var(--line); margin:18px 0">
        <div class="cfg-grid">
          <div class="field"><label>Prix Repas + Soirée (€)</label><input data-cfg="priceDine" type="number"></div>
          <div class="field"><label>Prix Soirée seule (€)</label><input data-cfg="priceParty" type="number"></div>
          <div class="field"><label>Prix À emporter (€)</label><input data-cfg="priceTake" type="number"></div>
          <div class="field"><label>Places à table (repas sur place)</label><input data-cfg="capDine" type="number"></div>
          <div class="field"><label>Total repas cuisinés max (table + emporter)</label><input data-cfg="capKitchen" type="number"></div>
          <div class="field"><label>Capacité totale soirée (table + soirée seule)</label><input data-cfg="capParty" type="number"></div>
        </div>
        <hr style="border:none; border-top:1px solid var(--line); margin:18px 0">
        <div class="cfg-grid">
          <div class="field"><label>IBAN (virement)</label><input data-cfg="iban"></div>
          <div class="field"><label>Titulaire du compte</label><input data-cfg="accountName"></div>
        </div>
        <p class="hint" style="color:var(--muted)">Le code d'accès organisateur se modifie dans le fichier <span class="mono">config.php</span> sur le serveur.</p>
        <button class="btn" id="saveCfg" style="margin-top:18px">Enregistrer les réglages</button>
        <span id="cfgSaved" style="margin-left:12px; color:var(--mint); font-size:.85rem"></span>
        <hr style="border:none; border-top:1px solid var(--line); margin:22px 0">
        <button class="btn ghost" id="wipeBtn" style="color:var(--coral); box-shadow:inset 0 0 0 1.5px var(--coral)">⚠ Effacer toutes les réservations</button>
      </div>
    </div>
  </div>
</div>

<footer>
  <div class="wrap">
    <div class="logo" style="justify-content:center; margin-bottom:8px"><span id="footName">Le Grand Repas</span><span class="dot" style="color:var(--coral)">.</span></div>
    <p>Réservation et paiement anticipés · Merci pour votre soutien 💛</p>
  </div>
</footer>

<script>
/* ============================================================
   Front-end — branché sur api.php (PHP/MySQL/Stripe/Brevo)
============================================================ */
const API = "api.php";
async function api(action, {method="GET", body=null} = {}){
  const opt = { method, credentials:"same-origin", headers:{} };
  if(body){ opt.headers["Content-Type"]="application/json"; opt.body=JSON.stringify(body); }
  const res = await fetch(API + "?action=" + action, opt);
  let data = {}; try { data = await res.json(); } catch(e){}
  return { ok: res.ok, status: res.status, data };
}

const FORMULAS = [
  { key:"dine",  cls:"t-coral", kind:"Formule complète", name:"Repas + Soirée DJ", color:"var(--coral)",
    bullets:["Repas complet à table","Accès à la soirée DJ","Place assise garantie"] },
  { key:"party", cls:"t-mint",  kind:"Sans repas", name:"Soirée DJ seule", color:"var(--mint)",
    bullets:["Accès à la soirée DJ","Ambiance dancefloor","Arrive quand tu veux"] },
  { key:"take",  cls:"t-gold",  kind:"À la maison", name:"Repas à emporter", color:"var(--gold)",
    bullets:["Le même bon repas","À enlever sur place","Heure au choix"] },
];

let CFG = {};
let REM = {dine:0,party:0,take:0};
let HEAD = {dineRoom:0,kitchen:0,party:0};
let RAISED = {cents:0,count:0};
let STRIPE = false;
let cart = { dine:0, party:0, take:0 };

const euro = n => (Math.round(n*100)/100).toLocaleString("fr-BE",{minimumFractionDigits:0}) + " €";
const euroC = c => (Math.round(c)/100).toLocaleString("fr-BE",{minimumFractionDigits:2}) + " €";

async function loadState(){
  const { data } = await api("state");
  if(!data || !data.config) return;
  CFG = data.config; REM = data.remaining; HEAD = data.head;
  RAISED = {cents:data.raisedCents, count:data.raisedCount};
  STRIPE = !!data.stripeEnabled;
  applyConfigToDOM(); renderPublic();
}

function applyConfigToDOM(){
  document.getElementById("brandLogo").innerHTML = CFG.eventName + '<span class="dot">.</span>';
  document.getElementById("footName").textContent = CFG.eventName;
  document.getElementById("mDate").textContent = CFG.date;
  document.getElementById("mTime").textContent = CFG.time;
  document.getElementById("mPlace").textContent = CFG.place;
  document.getElementById("causeText1").innerHTML =
    `Chaque billet vendu finance directement <b>${CFG.cause}</b>. Le repas est préparé avec amour par notre cuisinier bénévole, et la soirée est animée par un DJ qui reverse sa prestation à la cause.`;
  document.title = CFG.eventName + " · Soirée DJ caritative";
  const pc = document.getElementById("payCard");
  if(pc) pc.style.display = STRIPE ? "" : "none";
}

function maxFor(key){
  const cd=cart.dine, cp=cart.party, ct=cart.take;
  let m;
  if(key==="dine")       m = Math.min(HEAD.dineRoom, HEAD.kitchen - ct, HEAD.party - cp);
  else if(key==="party") m = HEAD.party - cd;
  else                   m = HEAD.kitchen - cd;
  return Math.max(0, m);
}

function renderFormulas(){
  const priceOf = {dine:CFG.priceDine, party:CFG.priceParty, take:CFG.priceTake};
  const remOf = {dine:REM.dine, party:REM.party, take:REM.take};
  const capOf = {dine:CFG.capDine, party:CFG.capParty, take:CFG.capKitchen};
  document.getElementById("formulaCards").innerHTML = FORMULAS.map(f=>{
    const rem = remOf[f.key], cap = capOf[f.key];
    const pct = cap>0 ? Math.max(4, Math.round(rem/cap*100)) : 0;
    const sold = rem<=0;
    return `<div class="ticket ${f.cls}">
      ${sold?'<div class="soldout-tag">COMPLET</div>':''}
      <span class="notch t"></span><span class="notch b"></span><span class="stub"></span>
      <div class="kind">${f.kind}</div>
      <h3>${f.name}</h3>
      <div class="price">${euro(priceOf[f.key])}<small> / billet</small></div>
      <ul>${f.bullets.map(b=>`<li>${b}</li>`).join("")}</ul>
      <div class="avail">
        <div class="availbar"><i style="width:${pct}%"></i></div>
        <div class="availtxt"><span>${sold?'Complet':rem+' place'+(rem>1?'s':'')+' restante'+(rem>1?'s':'')}</span><span>${cap} max</span></div>
      </div>
    </div>`;
  }).join("");
}

function renderQtyRows(){
  const list = document.getElementById("qtyList");
  if(!list) return;                 // le formulaire a été remplacé par le ticket de confirmation
  const priceOf = {dine:CFG.priceDine, party:CFG.priceParty, take:CFG.priceTake};
  list.innerHTML = FORMULAS.map(f=>{
    const max = maxFor(f.key);
    const free = Math.max(0, max - cart[f.key]);
    const sub = max<=0 ? "Complet" : free+" dispo · "+euro(priceOf[f.key]);
    return `<div class="qty-row">
      <span class="dot" style="background:${f.color}"></span>
      <div class="info"><b>${f.name}</b><span>${sub}</span></div>
      <div class="stepper">
        <button data-step="${f.key}" data-d="-1" aria-label="moins" ${cart[f.key]<=0?'disabled':''}>–</button>
        <input id="qty-${f.key}" value="${cart[f.key]}" inputmode="numeric" aria-label="${f.name} quantité">
        <button data-step="${f.key}" data-d="1" aria-label="plus" ${cart[f.key]>=max?'disabled':''}>+</button>
      </div>
    </div>`;
  }).join("");
}

function renderSummary(){
  const rowsEl = document.getElementById("sumRows");
  if(!rowsEl) return;               // récapitulatif absent (ticket affiché)
  const priceOf = {dine:CFG.priceDine, party:CFG.priceParty, take:CFG.priceTake};
  let rows="", total=0, any=false;
  for(const f of FORMULAS){
    const q = cart[f.key];
    if(q>0){ any=true; const line=q*priceOf[f.key]; total+=line;
      rows += `<div class="sumrow"><span>${q} × ${f.name}</span><span>${euro(line)}</span></div>`; }
  }
  if(!any) rows = `<div class="sumrow"><span>Aucun billet sélectionné</span><span>—</span></div>`;
  rowsEl.innerHTML = rows;
  document.getElementById("sumTotal").textContent = euro(total);
  const pc=document.getElementById("payCard"), pt=document.getElementById("payTransfer");
  if(pc) pc.disabled=!any; if(pt) pt.disabled=!any;
}

function renderPublic(){ renderFormulas(); renderQtyRows(); renderSummary(); }

function setQty(key, val){
  let v = parseInt(val||0,10); if(isNaN(v)||v<0) v=0;
  cart[key]=0;
  cart[key] = Math.min(v, maxFor(key));
  renderQtyRows(); renderSummary();
}

async function book(method){
  const name = document.getElementById("fName").value.trim();
  const email= document.getElementById("fEmail").value.trim();
  const phone= document.getElementById("fPhone").value.trim();
  const notes= document.getElementById("fNotes").value.trim();
  const err  = document.getElementById("formErr"); err.textContent="";

  if(!name){ err.textContent="Indique ton nom."; return; }
  if(!email && !phone){ err.textContent="Laisse un e-mail ou un téléphone."; return; }
  if(method==="stripe" && !email){ err.textContent="Un e-mail est requis pour le paiement par carte."; return; }
  if(cart.dine+cart.party+cart.take<=0){ err.textContent="Choisis au moins un billet."; return; }

  const btns=[document.getElementById("payCard"),document.getElementById("payTransfer")];
  btns.forEach(b=>{ if(b){b.disabled=true;} });
  err.textContent="Traitement en cours…";

  const { data, status } = await api("book", { method:"POST", body:{
    name, email, phone, notes, qd:cart.dine, qp:cart.party, qt:cart.take, method
  }});

  if(status===409 && data.error==="sold"){
    err.textContent="Oups, des places viennent de partir. On a réajusté les disponibilités.";
    await loadState(); return;
  }
  if(!data.ok){
    const msg = {name:"Nom manquant.",contact:"Contact manquant.",empty:"Aucun billet.",email_required:"E-mail requis pour la carte."}[data.error] || "Une erreur est survenue, réessaie.";
    err.textContent=msg; renderSummary(); return;
  }
  if(data.mode==="stripe"){ window.location.href = data.url; return; }
  showTicket(data.booking, data.warning);
  cart={dine:0,party:0,take:0};
  await loadState();
}

function showTicket(b, warning){
  const lines = FORMULAS.filter(f=>b.items[f.key]>0).map(f=>`${b.items[f.key]} × ${f.name}`).join(" · ");
  const mailNote = b.email ? "Ces infos t'ont aussi été envoyées par e-mail." : "";
  document.getElementById("bookingArea").innerHTML = `
    <div class="result">
      <div class="check">✓</div>
      <h3 style="font-size:1.5rem">C'est noté, ${b.name.split(" ")[0]} !</h3>
      <p style="color:#5a4d36; margin:6px 0 0">${lines}</p>
      ${warning?`<p style="color:#b8860b; font-size:.9rem">${warning}</p>`:''}
      <div class="pay-box">
        <p style="margin:0 0 4px"><span class="l">Montant à virer</span></p>
        <p class="v" style="font-size:1.6rem; margin:0 0 14px">${euroC(b.amount)}</p>
        <p style="margin:0 0 4px"><span class="l">IBAN</span></p>
        <p class="v">${CFG.iban} <button class="copybtn" data-copy="${CFG.iban}">copier</button></p>
        <p style="margin:12px 0 4px"><span class="l">Communication structurée</span></p>
        <p class="v">${b.ref} <button class="copybtn" data-copy="${b.ref}">copier</button></p>
        <p style="margin:12px 0 0"><span class="l">Bénéficiaire</span></p>
        <p class="v" style="font-size:.95rem">${CFG.accountName}</p>
      </div>
      <p style="font-size:.9rem; color:#5a4d36">Effectue le virement avec <b>exactement cette communication</b>. Ta place est confirmée dès réception. ${mailNote}</p>
      <button class="btn ghost" onclick="location.href='index.php'" style="margin-top:16px; box-shadow:inset 0 0 0 1.5px #D8C9A8; color:var(--ink)">Faire une autre réservation</button>
    </div>`;
  document.getElementById("reserver").scrollIntoView({behavior:"smooth"});
}

async function handleReturn(){
  const p = new URLSearchParams(location.search);
  const st = p.get("status"), ref = p.get("ref");
  if(!st) return;
  const banner = document.getElementById("statusBanner");
  const wrap = (bg,txt)=>`<div style="background:${bg};color:#1A1226;text-align:center;padding:14px 18px;font-weight:600">${txt}</div>`;
  if(st==="cancel"){
    banner.innerHTML = wrap("#F5B92E","Paiement annulé — la place a été libérée. Tu peux réessayer ci-dessous.");
    if(ref){ try{ await api("cancel_pending", {method:"POST", body:{ref}}); }catch(e){} }
    loadState();
    return;
  }
  if(st==="success" && ref){
    banner.innerHTML = wrap("#3DD6C4","Merci ! On confirme ton paiement…");
    for(let i=0;i<5;i++){
      const { data } = await api("lookup&ref="+encodeURIComponent(ref));
      if(data && data.status==="paid"){
        banner.innerHTML = wrap("#3DD6C4",`Paiement reçu — place confirmée ✅ Un e-mail de confirmation t'a été envoyé. Réf ${data.ref}`);
        loadState(); return;
      }
      await new Promise(r=>setTimeout(r,1500));
    }
    banner.innerHTML = wrap("#3DD6C4","Paiement reçu — la confirmation arrive par e-mail dans un instant. 🎉");
    loadState();
  }
}

/* ===================== ADMIN ===================== */
let ADMIN = { bookings:[], caps:{}, used:{}, remaining:{}, raisedCents:0, config:{} };
let activeTab = "resa";
function openAdmin(){ document.getElementById("adminModal").classList.add("open"); }
function closeAdmin(){ document.getElementById("adminModal").classList.remove("open"); }

async function adminLogin(){
  const pass = document.getElementById("adminPass").value;
  const { data } = await api("admin_login", { method:"POST", body:{ password:pass } });
  if(data.ok){
    document.getElementById("adminGate").style.display="none";
    document.getElementById("adminDash").style.display="block";
    await loadAdmin();
  } else {
    document.getElementById("adminErr").textContent="Code incorrect.";
  }
}

async function loadAdmin(){
  const { data, status } = await api("admin_state");
  if(status===401){
    document.getElementById("adminGate").style.display="block";
    document.getElementById("adminDash").style.display="none";
    return;
  }
  ADMIN = data;
  renderAdmin();
}

function badge(s){
  if(s==="paid") return '<span class="pill paid">Payé</span>';
  if(s==="cancelled") return '<span class="pill cancel">Annulé</span>';
  return '<span class="pill pending">À payer</span>';
}

function renderAdmin(){
  const B = ADMIN.bookings||[];
  const used = ADMIN.used||{dine:0,meals:0,party:0};
  const caps = ADMIN.caps||{};
  const live = B.filter(b=>b.status!=="cancelled");
  const potential = live.reduce((s,b)=>s+b.amount,0);
  document.getElementById("statBoxes").innerHTML = `
    <div class="stat"><div class="n">${euroC(ADMIN.raisedCents)}</div><div class="k">Encaissé</div></div>
    <div class="stat"><div class="n">${euroC(potential-ADMIN.raisedCents)}</div><div class="k">En attente</div></div>
    <div class="stat"><div class="n">${live.length}</div><div class="k">Réservations</div></div>
    <div class="stat"><div class="n">${used.meals}</div><div class="k">Repas à prévoir</div></div>`;
  const g = [
    {lbl:"Repas à table", used:used.dine, cap:caps.capDine, col:"var(--coral)"},
    {lbl:"Repas cuisinés (table+emporter)", used:used.meals, cap:caps.capKitchen, col:"var(--gold)"},
    {lbl:"Soirée (présents)", used:used.party, cap:caps.capParty, col:"var(--mint)"},
  ];
  document.getElementById("gaugeBoxes").innerHTML = g.map(x=>{
    const pct = x.cap>0?Math.min(100,Math.round(x.used/x.cap*100)):0;
    return `<div class="gauge"><div class="top"><span>${x.lbl}</span><b>${x.used}/${x.cap}</b></div>
      <div class="bar"><i style="width:${pct}%; background:${x.col}"></i></div></div>`;
  }).join("");
  renderResaTable();
  renderKitchen();
}

function renderResaTable(){
  const q = (document.getElementById("search").value||"").toLowerCase();
  const fs = document.getElementById("filterStatus").value;
  const lbl = {dine:"Repas+Soirée", party:"Soirée", take:"Emporter"};
  const rows = (ADMIN.bookings||[]).filter(b=>{
    if(fs!=="all" && b.status!==fs) return false;
    if(q){ const hay=((b.name||"")+" "+(b.email||"")+" "+(b.phone||"")+" "+b.ref).toLowerCase(); if(!hay.includes(q)) return false; }
    return true;
  });
  document.getElementById("resaBody").innerHTML = rows.length ? rows.map(b=>{
    const items = Object.entries(b.items).filter(([k,v])=>v>0).map(([k,v])=>`${v} ${lbl[k]}`).join(", ");
    const tag = b.method==="stripe"?'<span style="font-size:.66rem;color:var(--mint)"> ⬤ carte</span>':'';
    return `<tr>
      <td class="mono" style="font-size:.78rem">${b.ref}</td>
      <td>${b.name}${tag}${b.notes?`<br><span style="color:var(--muted); font-size:.78rem">📝 ${b.notes}</span>`:''}</td>
      <td style="font-size:.8rem">${b.email||''}${b.email&&b.phone?'<br>':''}${b.phone||''}</td>
      <td style="font-size:.82rem">${items}</td>
      <td><b>${euroC(b.amount)}</b></td>
      <td>${badge(b.status)}</td>
      <td class="tbtns">
        ${b.status!=="paid"?`<button data-act="paid" data-id="${b.id}">✓ Payé</button>`:''}
        ${b.status==="paid"?`<button data-act="pending" data-id="${b.id}">↩ Non payé</button>`:''}
        ${b.status!=="cancelled"?`<button data-act="cancelled" data-id="${b.id}">✕ Annuler</button>`:`<button data-act="pending" data-id="${b.id}">↩ Rétablir</button>`}
      </td>
    </tr>`;
  }).join("") : `<tr><td colspan="7" style="text-align:center; color:var(--muted); padding:30px">Aucune réservation.</td></tr>`;
}

function renderKitchen(){
  const live = (ADMIN.bookings||[]).filter(b=>b.status!=="cancelled");
  const paid = b=>b.status==="paid";
  const dineMeals=live.reduce((s,b)=>s+b.items.dine,0), takeMeals=live.reduce((s,b)=>s+b.items.take,0);
  const dinePaid=live.filter(paid).reduce((s,b)=>s+b.items.dine,0), takePaid=live.filter(paid).reduce((s,b)=>s+b.items.take,0);
  document.getElementById("kitchenStats").innerHTML = `
    <div class="stat"><div class="n">${dinePaid}<span style="font-size:.9rem; color:var(--muted)">/${dineMeals}</span></div><div class="k">Repas à table (payés/total)</div></div>
    <div class="stat"><div class="n">${takePaid}<span style="font-size:.9rem; color:var(--muted)">/${takeMeals}</span></div><div class="k">À emporter (payés/total)</div></div>
    <div class="stat"><div class="n">${dinePaid+takePaid}</div><div class="k">Repas confirmés total</div></div>
    <div class="stat"><div class="n">${(ADMIN.caps||{}).capKitchen||0}</div><div class="k">Maximum cuisine</div></div>`;
  const li = (arr,qk)=>arr.filter(b=>b.items[qk]>0).map(b=>`<li>${paid(b)?'✅':'⏳'} ${b.name} — ${b.items[qk]} ${qk==='dine'?('couvert'+(b.items[qk]>1?'s':'')):'repas'}${b.notes?` <span style="color:var(--muted)">(${b.notes})</span>`:''}</li>`).join("");
  document.getElementById("listDine").innerHTML = li(live,'dine') || '<li style="color:var(--muted)">Personne pour l\'instant.</li>';
  document.getElementById("listTake").innerHTML = li(live,'take') || '<li style="color:var(--muted)">Personne pour l\'instant.</li>';
}

async function adminAction(id, status){
  await api("admin_update", { method:"POST", body:{ id:Number(id), status } });
  await loadAdmin(); loadState();
}

function exportCSV(){
  const head = ["Reference","Nom","Email","Telephone","Repas+Soiree","Soiree","Emporter","Montant_EUR","Statut","Methode","Date","Notes"];
  const sl = {paid:"Paye", pending:"A payer", cancelled:"Annule"};
  const ml = {stripe:"Carte", transfer:"Virement"};
  const rows = (ADMIN.bookings||[]).map(b=>[
    b.ref,b.name,b.email||"",b.phone||"",b.items.dine,b.items.party,b.items.take,
    (b.amount/100).toFixed(2),sl[b.status],ml[b.method],b.created,(b.notes||"").replace(/[\n;]/g," ")
  ].map(v=>`"${String(v).replace(/"/g,'""')}"`).join(";"));
  const csv = "\uFEFF"+[head.join(";"),...rows].join("\n");
  const blob = new Blob([csv],{type:"text/csv;charset=utf-8"});
  const a=document.createElement("a"); a.href=URL.createObjectURL(blob);
  a.download="reservations_"+(CFG.eventName||"event").replace(/\s+/g,"_")+".csv"; a.click();
}

function fillCfgForm(){
  document.querySelectorAll("[data-cfg]").forEach(inp=>{
    const k=inp.dataset.cfg; inp.value = (ADMIN.config && ADMIN.config[k]!=null) ? ADMIN.config[k] : "";
  });
}
async function saveCfg(){
  const numKeys=["goal","priceDine","priceParty","priceTake","capDine","capKitchen","capParty"];
  const body={};
  document.querySelectorAll("[data-cfg]").forEach(inp=>{
    const k=inp.dataset.cfg;
    body[k] = numKeys.includes(k) ? (parseFloat(inp.value)||0) : inp.value;
  });
  await api("admin_config", { method:"POST", body });
  await loadAdmin(); await loadState();
  const s=document.getElementById("cfgSaved"); s.textContent="✓ Enregistré"; setTimeout(()=>s.textContent="",2000);
}
async function wipeAll(){
  if(!confirm("Effacer définitivement TOUTES les réservations ?")) return;
  await api("admin_wipe", { method:"POST", body:{} });
  await loadAdmin(); loadState();
}

/* ===================== ÉVÉNEMENTS ===================== */
document.addEventListener("click", (e)=>{
  const t=e.target;
  if(t.dataset.step){ const k=t.dataset.step; setQty(k, cart[k]+parseInt(t.dataset.d,10)); }
  if(t.dataset.copy){ navigator.clipboard?.writeText(t.dataset.copy); t.textContent="copié ✓"; setTimeout(()=>t.textContent="copier",1500); }
  if(t.dataset.act && t.dataset.id){ adminAction(t.dataset.id, t.dataset.act); }
  if(t.hasAttribute("data-close") || t.id==="adminModal"){ closeAdmin(); }
  if(t.dataset.tab){
    activeTab=t.dataset.tab;
    document.querySelectorAll(".tabs button").forEach(b=>b.classList.toggle("on", b.dataset.tab===activeTab));
    document.querySelectorAll("[data-pane]").forEach(p=>p.style.display = p.dataset.pane===activeTab?"block":"none");
    if(activeTab==="reglages") fillCfgForm();
  }
});
document.getElementById("payCard").addEventListener("click", ()=>book("stripe"));
document.getElementById("payTransfer").addEventListener("click", ()=>book("transfer"));
document.getElementById("adminOpen").addEventListener("click", openAdmin);
document.getElementById("adminLogin").addEventListener("click", adminLogin);
document.getElementById("adminPass").addEventListener("keydown", e=>{ if(e.key==="Enter") adminLogin(); });
document.getElementById("refreshBtn").addEventListener("click", loadAdmin);
document.getElementById("search").addEventListener("input", renderResaTable);
document.getElementById("filterStatus").addEventListener("change", renderResaTable);
document.getElementById("csvBtn").addEventListener("click", exportCSV);
document.getElementById("saveCfg").addEventListener("click", saveCfg);
document.getElementById("wipeBtn").addEventListener("click", wipeAll);
document.addEventListener("input", e=>{ if(e.target.id && e.target.id.startsWith("qty-")){ setQty(e.target.id.slice(4), e.target.value); } });

(function(){
  const icons=["🍽️","🎧","🎶","🥂","🥡","✨","💛"];
  const f=document.getElementById("floaters");
  for(let i=0;i<7;i++){ const s=document.createElement("span");
    s.textContent=icons[i]; s.style.left=(8+Math.random()*84)+"%"; s.style.top=(10+Math.random()*70)+"%";
    s.style.animationDelay=(Math.random()*5)+"s"; f.appendChild(s); }
})();

(async function init(){
  await loadState();
  handleReturn();
})();

</script>
</body>
</html>
