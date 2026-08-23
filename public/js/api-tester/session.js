/** Updates the floating widget and inline session label. */

export function showLoggedInUser(user) {
    setSessionElements({
        loggedIn: true,
        name: user.name,
        email: user.email,
        role: user.role,
        inlineText: `${user.name} (${user.role}) — ${user.email}`,
    });
}

export function showLoggedOutUser() {
    setSessionElements({
        loggedIn: false,
        name: 'Not logged in',
        email: 'Login via Auth APIs or quick buttons',
        inlineText: '— none —',
    });
}

function setSessionElements({ loggedIn, name, email, role, inlineText }) {
    const inline = document.getElementById('session-user');
    const widget = document.getElementById('session-widget');
    const dot = document.getElementById('session-dot');
    const logoutBtn = document.getElementById('widget-logout');
    const roleBadge = document.getElementById('widget-user-role');

    inline.textContent = inlineText;
    inline.className = loggedIn ? `role-${role}` : '';

    document.getElementById('widget-user-name').textContent = name;
    document.getElementById('widget-user-email').textContent = email;

    widget.classList.toggle('logged-in', loggedIn);
    widget.classList.toggle('logged-out', !loggedIn);
    dot.classList.toggle('on', loggedIn);
    dot.classList.toggle('off', !loggedIn);
    logoutBtn.style.display = loggedIn ? 'inline-block' : 'none';

    if (loggedIn) {
        roleBadge.textContent = role;
        roleBadge.className = `session-role-badge ${role}`;
        roleBadge.style.display = 'inline-block';
    } else {
        roleBadge.style.display = 'none';
    }
}

export function updateSessionUI(user) {
    if (user) showLoggedInUser(user);
    else showLoggedOutUser();
}
