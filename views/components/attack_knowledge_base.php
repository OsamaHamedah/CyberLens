<?php
//the extension used is .php instead of .html to be able to add php code
// + for future-proof purposes
//I brought the images/logos next to each title from the keyboard emoji
/** * @var mysqli $conn */
if(!function_exists('hasResearch')) {
    function hasResearch($conn, $categoryCode)
    {
        $sql = "SELECT 1 FROM research WHERE category = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $categoryCode);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
   /* if($row = $result->fetch_assoc()) {
        return $row;
    } else {
        return null;
    } */

if(!function_exists('getBadgeClass')) {
  function getBadgeClass($severity) {
        switch (strtolower($severity)) {
            case 'critical': return 'badge-critical';
            case 'high': return 'badge-high';
            case 'medium': return 'badge-medium';
            case 'low': return 'badge-low';
            default: return 'badge-medium';
        }
    }
}
?>

<div class="kb-container">
    <div class="kb-header">
        <h3 style="margin: 0; color: #fff;">🛡️ Attack Knowledge Base</h3>
        <span style="font-size: 0.8em; color: gray;">Verified by CVEdetails.com</span>
    </div>
    <div class="kb-grid">

        <?php $hasMalware = hasResearch($conn, 'Malware'); ?>
        <div class="category-card">
            <h4 class="category-title">🦠 Malware</h4>
            <p class="category-desc">Malicious software like ransomware, viruses, worms, & spyware.</p>
            <div class="sub-win-container">
                <?php if($hasMalware): ?>
                <a href ="views/category_feed.php?cat=Malware" class="sub-win" style="border-left-color: #ff4d4d; cursor: pointer;">
                    <span class="sub-title">Explore Malware</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class ="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet...</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasPhishing = hasResearch($conn, 'Phishing'); ?>
        <div class="category-card">
            <h4 class="category-title">🎣 Phishing & Social Eng</h4>
            <p class ="category-desc">Tricking people into revealing information (spear phishing, whaling, BEC).</p>
            <div class="sub-win-container">
                <?php if($hasPhishing): ?>
                <a href ="views/category_feed.php?cat=Phishing" class="sub-win" style="border-left-color: #ff9f43; cursor: pointer;">
                    <span class="sub-title">Explore Phishing</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class ="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888">No Research yet..</span>
            </div>
                <?php endif; ?>
        </div>
        </div>


        <?php $hasDoS=hasResearch($conn, 'DoS'); ?>
        <div class="category-card">
            <h4 class="category-title">🧟 Denial-of-Service</h4>
            <p class="category-desc">Overwhelming systems to make them unavailable.</p>
            <div class="sub-win-container">
                <?php if($hasDoS): ?>
                <a href ="views/category_feed.php?cat=DoS" class="sub-win" style="border-left-color: #feca57; cursor: pointer;">
                    <span class="sub-title">Explore DoS</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class ="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasSQLi= hasResearch($conn, 'SQLi'); ?>
        <div class="category-card">
            <h4 class="category-title">💉 SQL Injection</h4>
            <p class = "category-desc">Injecting SQL queries into database forms.</p>
            <div class="sub-win-container">

                 <?php if($hasSQLi): ?>
                <a href ="views/category_feed.php?cat=SQLi" class="sub-win" style="border-left-color: #ff4d4d; cursor: pointer;">
                    <span class="sub-title">Explore SQLi</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class ="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasXSS= hasResearch($conn, 'XSS'); ?>
        <div class="category-card">
            <h4 class="category-title">❌ XSS</h4>
            <p class = "category-desc">Injecting malicious scripts into trusted websites.</p>
            <div class="sub-win-container">
                <?php if($hasXSS): ?>
                <a href ="views/category_feed.php?cat=XSS" class="sub-win" style="border-left-color: #ff9f43; cursor: pointer;">
                    <span class="sub-title">Explore XSS</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasMITM= hasResearch($conn, 'MITM'); ?>
        <div class="category-card">
            <h4 class="category-title">🕵️ Man-in-the-middle</h4>
            <p class="category-desc">Intercepting communication between two parties.</p>
            <div class="sub-win-container">
                <?php if($hasMITM): ?>
                <a href ="views/category_feed.php?cat=MITM" class="sub-win" style="border-left-color: #ff9f43; cursor: pointer;">
                    <span class="sub-title">Explore MITM</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class ="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasPassword= hasResearch($conn, 'Password'); ?>
        <div class="category-card">
            <h4 class="category-title">🔑 Password Attacks</h4>
            <p class = "category-desc">Brute-forcing, keylogging, or credential stuffing to steal information.</p>
            <div class="sub-win-container">
                <?php if($hasPassword): ?>
                <a href ="views/category_feed.php?cat=Password" class="sub-win" style="border-left-color: #feca57; cursor: pointer;">
                    <span class="sub-title">Explore Pass. Attacks</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasSupply=hasResearch($conn, 'SupplyChain'); ?>
        <div class="category-card">
            <h4 class="category-title">⛓️ Supply Chain Attacks</h4>
            <p class = "category-desc">Targeting trusted third-party vendors to access a primary target.</p>
            <div class="sub-win-container">
                <?php if($hasSupply): ?>
                <a href ="views/category_feed.php?cat=SupplyChain" class="sub-win" style="border-left-color: #ff4d4d; cursor: pointer;">
                    <span class="sub-title">Explore Supply Chain</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class ="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>


        <?php $hasZero=hasResearch($conn, 'ZeroDay'); ?>
        <div class="category-card">
            <h4 class="category-title">💣 Zero-Day Exploits</h4>
            <p class="category-desc">Attacking newly discovered software vulnerabilities before a patch exists.</p>
            <div class="sub-win-container">
                <?php if($hasZero): ?>
                <a href ="views/category_feed.php?cat=ZeroDay" class="sub-win" style="border-left-color: #ff4d4d; cursor: pointer;">
                    <span class="sub-title">Explore Zero-Day</span>
                    <span class="badge badge-high">View All</span>
                </a>
                <?php else: ?>
                <div class="sub-win" style="border-left-color: #555; opacity: 0.6; cursor: default;">
                    <span class ="sub-title" style="color: #888;">No Research yet..</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>