const postsContainer = document.getElementById('posts');
const threadId = document.querySelector('input[name="thread_id"]').value;
const newPostForm = document.getElementById('new-post-form');


function escapeHTML(str) {
    return str.replace(/[&<>"']/g, function (match) {
        const escape = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return escape[match];
    });
}

function fetchPosts() {
    fetch('get_posts.php?thread_id=' + threadId)
        .then(res => res.json())
        .then(data => {
            postsContainer.innerHTML = '';
            data.forEach(post => {
                const div = document.createElement('div');
                div.className = 'post';
                div.innerHTML = `
                    <strong>${escapeHTML(post.username)}</strong>: 
                    ${escapeHTML(post.content)} 
                     <span class="time">${post.created_at}</span>
                `;
                postsContainer.appendChild(div);
            });
        });
}

// Poll every 3 seconds
setInterval(fetchPosts, 3000);
fetchPosts();

// Submit new post
newPostForm.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(newPostForm);

    fetch('submit_post.php', {
        method: 'POST',
        body: formData
    }).then(() => {
        newPostForm.reset();
        fetchPosts();
    });
});
