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

/**
 * ⚡ 漏洞点 2：文档查看权限验证
 *
 * 正常流程：通过 URL 传入 token 参数，后端计算文档的 MD5 与期望值比较
 * 漏洞：使用 === 比较，但 md5_compat() 对数组参数返回 null
 * 如果期望值恰好也是 null… 就可以绕过！
 *
 * 但在 PHP 8 中 md5(array) 会抛 TypeError，因此这里使用兼容包装。
 * PHP 7.x 中 md5(array) 直接返回 null。
 */

function md5_compat($input) {
    if (is_array($input)) {
        return null;
    }
    return md5($input);
}

$page_title = '文档中心';
$message = '';
$message_type = '';
$show_secret = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_hash = md5_compat($token);

    // ⚡ 漏洞核心：md5_compat 对数组返回 null
    // 如果文档的访问令牌是硬编码的，但代码又做了 === null 检查…
    // "reset_token" 文档的令牌期望值为 null（因为某些原因未设置）
    // 但代码使用了 === 比较，所以必须让 md5_compat(token) === null
    // → 传一个数组参数 token[]=xxx 即可！

    $expected_token = null;  // 这个文档的访问令牌未正确设置！

    if ($token_hash === $expected_token) {
        $message = '✅ 令牌验证通过！你有权限查看此文档。';
        $message_type = 'success';
        $show_secret = true;
    } else {
        $message = '❌ 令牌验证失败，你没有权限查看此文档。';
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTech — 文档中心</title>
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
                <a href="documents.php" class="nav-link active">文档中心</a>
                <?php if ($role === 'admin'): ?>
                    <a href="admin.php" class="nav-link">⚙ 管理后台</a>
                <?php endif; ?>
                <span class="nav-user">
                    <span class="user-avatar"><?= htmlspecialchars(substr($name, 0, 3)) ?></span>
                    <?= htmlspecialchars($name) ?>
                </span>
                <a href="logout.php" class="nav-link logout">退出</a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <h1>📄 文档中心</h1>
            <p>公司内部文档与资源库</p>
        </div>

        <!-- 文档列表 -->
        <div class="card">
            <h2>📚 文档列表</h2>
            <div class="doc-list">
                <div class="doc-item">
                    <div class="doc-icon">📋</div>
                    <div class="doc-info">
                        <h4>2026年度工作计划.pdf</h4>
                        <p>上传者：admin · 1.2 MB · 公开</p>
                    </div>
                    <a href="#" class="btn btn-small btn-disabled">需验证</a>
                </div>
                <div class="doc-item">
                    <div class="doc-icon">📋</div>
                    <div class="doc-info">
                        <h4>员工手册_v3.pdf</h4>
                        <p>上传者：admin · 3.5 MB · 公开</p>
                    </div>
                    <a href="#" class="btn btn-small" onclick="alert('该文档可直接访问。'); return false;">查看</a>
                </div>
                <div class="doc-item restricted">
                    <div class="doc-icon">🔒</div>
                    <div class="doc-info">
                        <h4>机密 — 系统安全审计报告</h4>
                        <p>上传者：admin · 0.8 MB · 🔐 需令牌验证</p>
                        <small class="hint-text">访问需要安全令牌，请联系管理员获取</small>
                    </div>
                    <a href="#" class="btn btn-small btn-warning" onclick="document.getElementById('token-section').scrollIntoView({behavior:'smooth'}); return false;">🔑 验证令牌</a>
                </div>
                <div class="doc-item restricted">
                    <div class="doc-icon">🔒</div>
                    <div class="doc-info">
                        <h4>机密 — 数据库备份策略</h4>
                        <p>上传者：admin · 0.5 MB · 🔐 需令牌验证</p>
                    </div>
                    <a href="#" class="btn btn-small btn-warning" onclick="document.getElementById('token-section').scrollIntoView({behavior:'smooth'}); return false;">🔑 验证令牌</a>
                </div>
            </div>
        </div>

        <!-- 令牌验证区 -->
        <div class="card" id="token-section">
            <h2>🔑 安全令牌验证</h2>
            <p>机密文档需要安全令牌才能查看。请输入管理员提供的令牌。</p>

            <form method="GET" action="documents.php" class="token-form">
                <div class="form-group">
                    <label for="token">访问令牌：</label>
                    <input type="text" id="token" name="token" class="form-input"
                           placeholder="输入令牌字符串…"
                           value="<?= isset($_GET['token']) && !is_array($_GET['token']) ? htmlspecialchars($_GET['token']) : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary">🔓 验证令牌</button>
            </form>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($show_secret): ?>
                <div class="secret-content">
                    <h3>📄 系统安全审计报告（节选）</h3>
                    <div class="secret-text">
                        <h4>发现的安全问题</h4>
                        <ul>
                            <li>登录模块使用了 <code>==</code> 而非 <code>===</code> 比较密码哈希</li>
                            <li>文档令牌验证对数组类型参数处理不当</li>
                            <li>建议立即修复以上漏洞并安排安全审计</li>
                        </ul>
                    </div>
                    <div class="flag-box">
                        <strong>FLAG_2:</strong>
                        <code>FLAG{MD5_array_token_bypass}</code>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- 提示（仅在未找到漏洞时有用） -->
        <div class="card card-tip">
            <h4>💡 提示</h4>
            <p>令牌验证使用的是 <code>===</code> 严格比较。</p>
            <p class="hint-text">想一想 PHP 中什么类型的值传给 <code>md5()</code> 会得不到一个正常的哈希字符串？</p>
        </div>
    </div>

    <footer>
        <p>NovaTech Internal Management System v2.4.1</p>
    </footer>
</body>
</html>
