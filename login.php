<?php
/**
 * 漏洞点 1：登录验证使用 == 比较 MD5 哈希
 * 存储的 admin 密码哈希是 0e462097431906509019562988736854（md5("240610708")）
 * 任何 md5 以 0e 开头的密码都能通过 == 比较
 *
 * 漏洞点 2：文档访问使用 === 比较，但处理数组时返回 null
 * 通过 token[]=xxx 传数组参数可使 md5 返回 null
 */

session_start();

// "数据库"中的用户
$users = [
    'admin' => [
        'name'     => 'Administrator',
        'password' => '0e462097431906509019562988736854',  // md5("240610708") — 0e 开头！
        'role'     => 'admin',
    ],
    'zhangsan' => [
        'name'     => '张三',
        'password' => 'e10adc3949ba59abbe56e057f20f883e',  // md5("123456")
        'role'     => 'user',
    ],
    'lisi' => [
        'name'     => '李四',
        'password' => 'e10adc3949ba59abbe56e057f20f883e',  // md5("123456")
        'role'     => 'user',
    ],
];

// 检查登录
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username])) {
        $user = $users[$username];

        // ⚡ 漏洞点：使用 == 而非 === 比较 MD5
        // 如果输入的密码的 MD5 以 0e 开头，且存储的哈希也是 0e 开头，
        // 两者 == 比较都会被视为 0，结果为 true！
        if (md5($password) == $user['password']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username']  = $username;
            $_SESSION['role']      = $user['role'];
            $_SESSION['name']      = $user['name'];
            header('Location: dashboard.php');
            exit;
        }
    }

    $error = '用户名或密码错误！';
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTech 内部管理系统 — 登录</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <span class="logo-icon">NT</span>
                </div>
                <h1>NovaTech 内部管理系统</h1>
                <p class="login-subtitle">员工登录入口</p>
            </div>

            <form method="POST" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" id="username" name="username" class="form-input"
                           placeholder="请输入用户名" required>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="请输入密码" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">登 录</button>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['logged_out'])): ?>
                    <div class="alert alert-success">✅ 您已成功退出系统。</div>
                <?php endif; ?>
            </form>

            <div class="login-footer">
                <p>默认账号：<code>zhangsan</code> / <code>123456</code></p>
                <p class="hint-text">🔒 管理员账号仅限授权人员使用</p>
            </div>
        </div>
    </div>
</body>
</html>
