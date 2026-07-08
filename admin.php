<?php
session_start();

// 仅管理员可访问
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTech — 管理后台</title>
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
                <a href="dashboard.php" class="nav-link">控制台</a>
                <a href="documents.php" class="nav-link">文档中心</a>
                <a href="admin.php" class="nav-link active">⚙ 管理后台</a>
                <a href="logout.php" class="nav-link logout">退出</a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <h1>⚙ 管理后台</h1>
            <p>系统管理面板 — 仅管理员可见</p>
        </div>

        <!-- 用户管理 -->
        <div class="card">
            <h2>👥 用户管理</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>姓名</th>
                        <th>角色</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>admin</td><td>Administrator</td><td><span class="badge-admin">管理员</span></td><td>在线</td></tr>
                    <tr><td>zhangsan</td><td>张三</td><td><span class="badge-user">普通用户</span></td><td>在线</td></tr>
                    <tr><td>lisi</td><td>李四</td><td><span class="badge-user">普通用户</span></td><td>离线</td></tr>
                </tbody>
            </table>
        </div>

        <!-- 机密信息 -->
        <div class="card card-flag">
            <h2>🏁 最高机密</h2>
            <div class="flag-content">
                <p>恭喜，你已以管理员身份成功登录系统！</p>
                <p>这证实了针对密码验证中 <code>==</code> 弱类型比较漏洞的利用是成功的。</p>
                <div class="flag-box">
                    <strong>FLAG_1:</strong>
                    <code>FLAG{MD5_0e_Login_Bypass_Success}</code>
                </div>
            </div>
        </div>

        <!-- 系统日志 -->
        <div class="card">
            <h2>📋 最近登录日志</h2>
            <table class="data-table">
                <thead>
                    <tr><th>用户</th><th>IP</th><th>时间</th><th>状态</th></tr>
                </thead>
                <tbody>
                    <tr><td>admin</td><td>10.0.0.1</td><td><?= date('Y-m-d H:i:s') ?></td><td><span class="status-ok">通过</span></td></tr>
                    <tr><td>zhangsan</td><td>10.0.0.23</td><td><?= date('Y-m-d H:i:s', strtotime('-1 hour')) ?></td><td><span class="status-ok">通过</span></td></tr>
                    <tr><td>unknown</td><td>192.168.1.105</td><td><?= date('Y-m-d H:i:s', strtotime('-2 hour')) ?></td><td><span class="status-fail">密码错误</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>NovaTech Internal Management System v2.4.1</p>
    </footer>
</body>
</html>
