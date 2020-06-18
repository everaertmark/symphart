const articles = document.getElementById('articles');

if (articles) {
    articles.addEventListener('click', (e) => {

        if (e.target.className === 'btn btn-danger delete-article') {

            e.preventDefault();

            const targetNode = e.target;

            if(confirm('Are you sure?')) {
                const id = e.target.getAttribute('data-id');

                fetch(`/article/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => targetNode.parentNode.parentNode.parentNode.removeChild(targetNode.parentNode.parentNode));
            }
        }
    });
}