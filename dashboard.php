<?php
session_start();

// 验证登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$name     = $_SESSION['name'];
$role     = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTech 内部管理系统 — 控制台</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-inner">
            <a href="dashboard.php" class="nav-brand">
                <span class="logo-icon small">NT</span>
                <span>内部管理系统</span>
            </a>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link active">控制台</a>
                <a href="documents.php" class="nav-link">文档中心</a>
                <?php if ($role === 'admin'): ?>
                    <a href="admin.php" class="nav-link">⚙ 管理后台</a>
                <?php endif; ?>
                <span class="nav-user">
                    <span class="user-avatar"><?= mb_substr($name, 0, 1) ?></span>
                    <?= htmlspecialchars($name) ?>
                    (<?= $role === 'admin' ? '管理员' : '普通用户' ?>)
                </span>
                <a href="logout.php" class="nav-link logout">退出</a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <h1>控制台总览</h1>
            <p>欢迎回来，<?= htmlspecialchars($name) ?>！</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">📄</div>
                <div class="stat-body">
                    <span class="stat-number">47</span>
                    <span class="stat-label">总文档数</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">👥</div>
                <div class="stat-body">
                    <span class="stat-number">12</span>
                    <span class="stat-label">在线员工</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">📋</div>
                <div class="stat-body">
                    <span class="stat-number">8</span>
                    <span class="stat-label">待办事项</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">🔒</div>
                <div class="stat-body">
                    <span class="stat-number">3</span>
                    <span class="stat-label">机密文件</span>
                </div>
            </div>
        </div>

        <!-- 公告栏 -->
        <div class="card">
            <h2>📢 公司公告</h2>
            <div class="announcement-list">
                <div class="announcement">
                    <span class="announcement-tag urgent">紧急</span>
                    <p>系统安全升级将于本周六凌晨 2:00-4:00 进行，届时系统将暂停访问。</p>
                    <small>2026-07-08</small>
                </div>
                <div class="announcement">
                    <span class="announcement-tag normal">通知</span>
                    <p>Q3 季度总结会议定于 7 月 15 日召开，请各部门提前准备汇报材料。</p>
                    <small>2026-07-06</small>
                </div>
                <div class="announcement">
                    <span class="announcement-tag normal">通知</span>
                    <p>所有员工请定期修改密码，确保账户安全。</p>
                    <small>2026-07-01</small>
                </div>
            </div>
        </div>

        <!-- 角色专用区块 -->
        <?php if ($role === 'admin'): ?>
            <div class="card card-admin">
                <h2>🔐 管理员专区</h2>
                <p>欢迎管理员！你有权限访问系统的全部功能。</p>
                <div class="admin-links">
                    <a href="admin.php" class="btn btn-primary">⚙ 进入管理后台</a>
                    <a href="documents.php" class="btn btn-secondary">📄 访问文档中心</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card card-limited">
                <h2>🔒 权限不足</h2>
                <p>当前账户为普通用户权限，部分功能受限。</p>
                <p class="hint-text">如需访问管理功能，请联系系统管理员。</p>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>NovaTech Internal Management System v2.4.1</p>
    </footer>
</body>
</html>
