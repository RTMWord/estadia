<?php
// public/admin/partials/admin_nav.php
// Reusable admin sidebar
$currentPage = basename((string)($_SERVER['PHP_SELF'] ?? ''));

$topNavLinks = [
  ['href' => 'index.php', 'label' => 'Dashboard'],
  ['href' => 'servicios.php', 'label' => 'Servicios'],
  ['href' => 'agencias.php', 'label' => 'Agencias'],
  ['href' => 'productos.php', 'label' => 'Productos'],
  ['href' => 'contenidos.php', 'label' => 'Contenidos'],
  ['href' => 'galeria.php', 'label' => 'Galeria'],
  ['href' => 'testimonios.php', 'label' => 'Testimonios'],
  ['href' => 'sugerencias.php', 'label' => 'Sugerencias'],
  ['href' => 'citas.php', 'label' => 'Citas'],
  ['href' => 'reportes.php', 'label' => 'Reportes'],
  ['href' => 'backup.php', 'label' => 'Backups'],
  ['href' => 'usuarios.php', 'label' => 'Usuarios'],
];

$adminMenu = [
    'General' => [
        ['href' => 'index.php', 'label' => 'Dashboard', 'icon' => 'fa-solid fa-gauge-high'],
        ['href' => 'usuarios.php', 'label' => 'Usuarios', 'icon' => 'fa-solid fa-user-group'],
        ['href' => 'agencias.php', 'label' => 'Agencias', 'icon' => 'fa-solid fa-building'],
        ['href' => 'servicios.php', 'label' => 'Servicios', 'icon' => 'fa-solid fa-gears'],
        ['href' => 'productos.php', 'label' => 'Productos', 'icon' => 'fa-solid fa-box-open'],
    ],
    'Contenido' => [
        ['href' => 'contenidos.php', 'label' => 'Contenidos', 'icon' => 'fa-solid fa-tags'],
        ['href' => 'galeria.php', 'label' => 'Galeria', 'icon' => 'fa-solid fa-image'],
        ['href' => 'testimonios.php', 'label' => 'Testimonios', 'icon' => 'fa-solid fa-comments'],
        ['href' => 'comunidad.php', 'label' => 'Comunidad', 'icon' => 'fa-solid fa-users'],
    ],
    'Operacion' => [
        ['href' => 'citas.php', 'label' => 'Citas', 'icon' => 'fa-solid fa-calendar-check'],
        ['href' => 'solicitudes_diagnostico.php', 'label' => 'Diagnosticos', 'icon' => 'fa-solid fa-clipboard-check'],
        ['href' => 'sugerencias.php', 'label' => 'Sugerencias', 'icon' => 'fa-solid fa-inbox'],
        ['href' => 'reportes.php', 'label' => 'Reportes', 'icon' => 'fa-solid fa-file-lines'],
    ],
    'Sitio' => [
        ['href' => 'backup.php', 'label' => 'Backups', 'icon' => 'fa-solid fa-floppy-disk'],
        ['href' => 'restore.php', 'label' => 'Restaurar', 'icon' => 'fa-solid fa-rotate-left'],
        ['href' => '../logout.php', 'label' => 'Cerrar sesion', 'icon' => 'fa-solid fa-right-from-bracket'],
    ],
];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root {
    --admin-sidebar-width: 286px;
    --admin-topbar-height: 54px;
    --admin-sidebar-bg: linear-gradient(180deg, #07224d 0%, #0f2f67 50%, #081d45 100%);
    --admin-sidebar-text: #e6eef7;
    --admin-sidebar-muted: #adc6dd;
    --admin-sidebar-active: #0f6edb;
    --admin-sidebar-border: rgba(255, 255, 255, .13);
  }

  html, body {
    margin: 0;
  }

  body.admin-with-sidebar {
    min-height: 100vh;
    padding-left: var(--admin-sidebar-width);
    padding-top: var(--admin-topbar-height);
  }

  .admin-topbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: var(--admin-topbar-height);
    z-index: 1150;
    background: #1f242b;
    border-bottom: 1px solid rgba(255, 255, 255, .08);
    display: flex;
    align-items: center;
    padding: 0 .9rem 0 calc(var(--admin-sidebar-width) + .9rem);
    gap: 1.35rem;
    overflow-x: auto;
    scrollbar-width: thin;
  }

  .admin-topbar-brand {
    color: #ffffff;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
    margin-right: .5rem;
    white-space: nowrap;
  }

  .admin-topbar-menu {
    display: flex;
    align-items: center;
    gap: 1.15rem;
    margin: 0 auto;
  }

  .admin-topbar-link {
    color: #e9f0f8;
    text-decoration: none;
    font-size: .93rem;
    font-weight: 600;
    white-space: nowrap;
    padding: .2rem .05rem;
    border-bottom: 2px solid transparent;
  }

  .admin-topbar-link:hover {
    color: #ffffff;
  }

  .admin-topbar-link.active {
    color: #ffffff;
    border-bottom-color: #0f6edb;
  }

  .admin-sidebar {
    position: fixed;
    top: var(--admin-topbar-height);
    left: 0;
    bottom: 0;
    width: var(--admin-sidebar-width);
    z-index: 1100;
    color: var(--admin-sidebar-text);
    background: var(--admin-sidebar-bg);
    box-shadow: 8px 0 24px rgba(0, 0, 0, .22);
    overflow-y: auto;
  }

  .admin-sidebar-header {
    padding: 1.4rem 1.1rem 1rem;
    text-align: center;
    border-bottom: 1px solid var(--admin-sidebar-border);
  }

  .admin-avatar {
    width: 108px;
    height: 108px;
    border-radius: 50%;
    margin: 0 auto .7rem;
    border: 3px solid rgba(255, 255, 255, .45);
    background: radial-gradient(circle at 30% 30%, #37b8cf 0%, #1d8ca6 70%);
    display: grid;
    place-items: center;
    font-size: 3rem;
    color: #ffffff;
  }

  .admin-role-name {
    margin: 0;
    font-size: 1.22rem;
    font-weight: 700;
  }

  .admin-role-sub {
    margin: .15rem 0 0;
    color: var(--admin-sidebar-muted);
    font-size: .92rem;
  }

  .admin-menu-section {
    margin-top: .65rem;
    padding: 0 .55rem;
  }

  .admin-menu-toggle {
    width: 100%;
    border: 1px solid transparent;
    background: transparent;
    color: var(--admin-sidebar-muted);
    border-radius: .6rem;
    padding: .55rem .62rem;
    margin: .65rem 0 .2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .7rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-size: .75rem;
    font-weight: 700;
    cursor: pointer;
  }

  .admin-menu-toggle:hover {
    color: #ffffff;
    border-color: rgba(255, 255, 255, .14);
    background: rgba(255, 255, 255, .07);
  }

  .admin-menu-toggle .chevron {
    font-size: .8rem;
    transition: transform .15s ease;
  }

  .admin-menu-links {
    display: none;
    padding: .2rem 0 0;
  }

  .admin-menu-section.is-open .admin-menu-links {
    display: block;
  }

  .admin-menu-section.is-open .admin-menu-toggle {
    color: #ffffff;
  }

  .admin-menu-section.is-open .admin-menu-toggle .chevron {
    transform: rotate(180deg);
  }

  .admin-menu-link {
    display: flex;
    align-items: center;
    gap: .65rem;
    text-decoration: none;
    color: var(--admin-sidebar-text);
    border-radius: .6rem;
    padding: .62rem .7rem;
    margin-bottom: .18rem;
    border: 1px solid transparent;
    transition: background .15s ease, transform .15s ease, border-color .15s ease;
  }

  .admin-menu-link i {
    width: 1.15rem;
    text-align: center;
    opacity: .92;
  }

  .admin-menu-link:hover {
    background: rgba(255, 255, 255, .09);
    border-color: rgba(255, 255, 255, .18);
    color: #ffffff;
    transform: translateX(3px);
  }

  .admin-menu-link.active {
    background: rgba(15, 110, 219, .98);
    border-color: rgba(255, 255, 255, .2);
    color: #ffffff;
    font-weight: 600;
  }

  .admin-mobile-toggle {
    display: none;
    position: fixed;
    top: 8px;
    left: 12px;
    z-index: 1201;
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 999px;
    background: #0f2f67;
    color: #fff;
    box-shadow: 0 6px 14px rgba(0, 0, 0, .28);
  }

  .admin-sidebar-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1090;
    background: rgba(0, 0, 0, .45);
  }

  @media (max-width: 991.98px) {
    body.admin-with-sidebar {
      padding-left: 0;
      padding-top: var(--admin-topbar-height);
    }

    .admin-topbar {
      left: 0;
      padding: 0 .8rem 0 3.4rem;
    }

    .admin-topbar-menu {
      margin: 0;
      gap: .9rem;
    }

    .admin-mobile-toggle {
      display: grid;
      place-items: center;
    }

    .admin-sidebar {
      top: 0;
      transform: translateX(-100%);
      transition: transform .2s ease;
      width: min(84vw, 290px);
    }

    body.admin-sidebar-open .admin-sidebar {
      transform: translateX(0);
    }

    body.admin-sidebar-open .admin-sidebar-backdrop {
      display: block;
    }
  }
</style>

<nav class="admin-topbar" aria-label="Navegacion principal admin">
  <a class="admin-topbar-brand" href="index.php">Admin Panel</a>
  <div class="admin-topbar-menu">
    <?php foreach ($topNavLinks as $topLink): ?>
      <?php
        $topHref = (string)$topLink['href'];
        $topPage = basename($topHref);
        $topActive = ($topPage === $currentPage);
      ?>
      <a href="<?= htmlspecialchars($topHref) ?>" class="admin-topbar-link<?= $topActive ? ' active' : '' ?>"><?= htmlspecialchars((string)$topLink['label']) ?></a>
    <?php endforeach; ?>
  </div>
</nav>

<button class="admin-mobile-toggle" id="adminSidebarToggle" type="button" aria-label="Abrir menu admin">
  <i class="fa-solid fa-bars"></i>
</button>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-header">
    <div class="admin-avatar" aria-hidden="true"><i class="fa-solid fa-user-tie"></i></div>
    <p class="admin-role-name">Administrador</p>
    <p class="admin-role-sub">Panel de control</p>
  </div>

  <?php foreach ($adminMenu as $section => $links): ?>
    <?php
      $sectionHasActive = false;
      foreach ($links as $linkCheck) {
          if (basename((string)$linkCheck['href']) === $currentPage) {
              $sectionHasActive = true;
              break;
          }
      }
      if (!$sectionHasActive && $section === 'General' && $currentPage === 'index.php') {
          $sectionHasActive = true;
      }
    ?>
    <nav class="admin-menu-section<?= $sectionHasActive ? ' is-open' : '' ?>" aria-label="<?= htmlspecialchars($section) ?>">
      <button class="admin-menu-toggle" type="button" aria-expanded="<?= $sectionHasActive ? 'true' : 'false' ?>">
        <span><?= htmlspecialchars($section) ?></span>
        <i class="fa-solid fa-chevron-down chevron" aria-hidden="true"></i>
      </button>
      <div class="admin-menu-links">
        <?php foreach ($links as $link): ?>
          <?php
            $href = (string)$link['href'];
            $targetPage = basename($href);
            $isActive = ($targetPage === $currentPage);
          ?>
          <a href="<?= htmlspecialchars($href) ?>" class="admin-menu-link<?= $isActive ? ' active' : '' ?>">
            <i class="<?= htmlspecialchars((string)$link['icon']) ?>" aria-hidden="true"></i>
            <span><?= htmlspecialchars((string)$link['label']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </nav>
  <?php endforeach; ?>
</aside>
<div class="admin-sidebar-backdrop" id="adminSidebarBackdrop" aria-hidden="true"></div>

<script>
  (function () {
    var body = document.body;
    if (!body) return;

    body.classList.add('admin-with-sidebar');

    var toggle = document.getElementById('adminSidebarToggle');
    var backdrop = document.getElementById('adminSidebarBackdrop');
    var sidebar = document.getElementById('adminSidebar');

    function closeSidebar() {
      body.classList.remove('admin-sidebar-open');
    }

    if (toggle) {
      toggle.addEventListener('click', function () {
        body.classList.toggle('admin-sidebar-open');
      });
    }

    if (backdrop) {
      backdrop.addEventListener('click', closeSidebar);
    }

    if (sidebar) {
      sidebar.addEventListener('click', function (event) {
        var toggleBtn = event.target.closest('.admin-menu-toggle');
        if (toggleBtn) {
          var section = toggleBtn.closest('.admin-menu-section');
          if (section) {
            var willOpen = !section.classList.contains('is-open');
            var allSections = sidebar.querySelectorAll('.admin-menu-section');
            allSections.forEach(function (item) {
              item.classList.remove('is-open');
              var btn = item.querySelector('.admin-menu-toggle');
              if (btn) btn.setAttribute('aria-expanded', 'false');
            });

            if (willOpen) {
              section.classList.add('is-open');
              toggleBtn.setAttribute('aria-expanded', 'true');
            }
          }
          return;
        }

        var link = event.target.closest('a');
        if (link && window.innerWidth < 992) {
          closeSidebar();
        }
      });
    }
  })();
</script>

