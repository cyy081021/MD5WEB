# MD5WEB

MD5绕过漏洞练习网站 — 一个模拟真实企业内网环境的 PHP 漏洞练习平台。

## 🔓 漏洞清单

### 漏洞 ①：登录认证绕过（`==` 弱类型比较）

**位置**：`login.php`

**漏洞原理**：密码验证使用 `==` 而非 `===` 比较 MD5 哈希值。当两个 MD5 值都以 `0e` 开头时，PHP 会将其解析为科学计数法（`0 × 10ⁿ = 0`），导致比较结果为 `true`。

```php
// 漏洞代码
if (md5($password) == $user['password']) {  // ← == 弱类型比较
```

**利用方式**：
- 用户名：`admin`
- 密码：`QNKCDZO`（其 MD5 为 `0e830400451993494058024219903391`）

获取 **FLAG_1**。

### 漏洞 ②：文档令牌验证绕过（`===` + 数组返回 null）

**位置**：`documents.php`

**漏洞原理**：当参数为数组时，`md5_compat()` 返回 `null`。而系统预期值恰好也是 `null`，使得 `null === null` 通过严格比较。

```php
$expected_token = null;  // 令牌未正确设置
if ($token_hash === $expected_token) {  // null === null → true
```

**利用方式**：
```
/documents.php?token[]=1
```

获取 **FLAG_2**。

## 🚀 启动方式

```bash
cd md5web
php -S 0.0.0.0:8080
```

访问 `http://localhost:8080`

### 测试账号

| 用户名 | 密码 | 角色 |
|--------|------|------|
| `zhangsan` | `123456` | 普通用户 |
| `lisi` | `123456` | 普通用户 |
| `admin` | 见漏洞 ① | 管理员 |

## 📁 项目结构

```
├── index.php          # 入口（自动跳转）
├── login.php          # 登录页（含漏洞 ①）
├── dashboard.php      # 控制台
├── admin.php          # 管理后台（需管理员权限）
├── documents.php      # 文档中心（含漏洞 ②）
├── logout.php         # 退出登录
├── style.css          # 样式
└── .gitignore
```

## ⚙️ 环境要求

- PHP 8.0+
- 无需数据库（使用内置数组模拟）
