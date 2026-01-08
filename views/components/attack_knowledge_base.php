<?php
//the extension is .php instead of .html for future-proof purposes
//I brought the images/logos next to each title from the keyboard emojis
?>

<div class="kb-container">
    <div class="kb-header">
        <h3 style="margin: 0; color: #fff;">🛡️ Attack Knowledge Base</h3>
        <span style="font-size: 0.8em; color: gray;">Verified by CVEdetails.com</span>
    </div>
    <div class="kb-grid">
        <div class="category-card">
            <h4 class="category-title">🦠 Malware</h4>
            <p class="category-desc">Malicious software like ransomware, viruses, worms, & spyware.</p>
            <div class="sub-win-container">

                <a href ="#" class="sub-win" style="border-left-color: #ff4d4d;">
                    <span class="sub-title">WannaCry Ransomware</span>
                    <span class="badge badge-critical">Critical</span>
                    </a>
            </div>
        </div>

        <div class="category-card">
            <h4 class="category-title">🎣 Phishing & Social Eng</h4>
            <p class ="category-desc">Tricking people into revealing information (spear phishing, whaling, BEC).</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #ff9f43">
                    <span class="sub-title">LinkedIn Spear Phishing</span>
                    <span class="badge badge-high">High</span>

                </a>
            </div>
        </div>

        <div class="category-card">
            <h4 class="category-title">🧟 Denial-of-Service</h4>
            <p class="category-desc">Overwhelming systems to make them unavailable.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #feca57">
                    <span class="sub-title">Mirai Botnet</span>
                    <span class="badge badge-medium">Medium</span>
                </a>
            </div>
        </div>
        <div class="category-card">
            <h4 class="category-title">💉 SQL Injection</h4>
            <p class = "category-desc">Injecting SQL queries into database forms.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #ff4d4d">
                    <span class="sub-title">Blind SQLi on Login</span>
                    <span class="badge badge-critical">Critical</span>
                </a>
            </div>
        </div>
        <div class="category-card">
            <h4 class="category-title">❌ XSS</h4>
            <p class = "category-desc">Injecting malicious scripts into trusted websites.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #ff9f43">
                    <span class="sub-title">Stored XSS in comments</span>
                    <span class="badge badge-high">High</span>
                </a>
            </div>
        </div>
        <div class="category-card">
            <h4 class="category-title">🕵️ Man-in-the-middle</h4>
            <p class="category-desc">Intercepting communication between two parties.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #ff9f43">
                    <span class="sub-title">SSL Stripping</span>
                    <span class="badge badge-high">High</span>
                </a>
            </div>
        </div>
        <div class="category-card">
            <h4 class="category-title">🔑 Password Attacks</h4>
            <p class = "category-desc">Brute-forcing, keylogging, or credential stuffing to steal information.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #feca57">
                    <span class="sub-title">Hydra SSH Brute Force</span>
                    <span class="badge badge-medium">Medium</span>
                </a>
            </div>
        </div>
        <div class="category-card">
            <h4 class="category-title">⛓️ Supply Chain Attacks</h4>
            <p class = "category-desc">Targeting trusted third-party vendors to access a primary target.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #ff4d4d">
                    <span class="sub-title">SolarWinds Hack</span>
                    <span class="badge badge-critical">Critical</span>
                </a>
            </div>
        </div>
        <div class="category-card">
            <h4 class="category-title">💣 Zero-Day Exploits</h4>
            <p class="category-desc">Attacking newly discovered software vulnerabilities before a patch exists.</p>
            <div class="sub-win-container">
                <a href ="#" class="sub-win" style="border-left-color: #ff4d4d">
                    <span class="sub-title">Log4Shell (Log4j)</span>
                    <span class="badge badge-critical">Critical</span>
                </a>
            </div>
        </div>
    </div>
</div>