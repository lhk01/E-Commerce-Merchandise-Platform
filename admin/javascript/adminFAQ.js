document.addEventListener('DOMContentLoaded', function () {
    const faqForm = document.querySelector('.faqForm');
    const faqList = document.getElementById('faqList');
    let editMode = false;
    let editId = null;

    // Fetch and display FAQs
    async function fetchFAQs() {
        try {
            const response = await fetch('../function/fetchFAQ.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ action: 'get' })
            });
            const data = await response.json();
            if (data.success) {
                faqList.innerHTML = '';
                data.faqs.forEach((faq, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td data-title='No'>${index + 1}</td>
                        <td data-title='Question'>${faq.question}</td>
                        <td data-title='Answer'>${faq.answer}</td>
                        <td data-title='Action'>
                            <a href="#" class="ed-button" onclick="editFAQ(${faq.id}, '${faq.question}', '${faq.answer}')">Edit</a>
                            <a href="#" class="ed-button" onclick="deleteFAQ(${faq.id})">Delete</a>
                        </td>
                    `;
                    faqList.appendChild(row);
                });
            }
        } catch (error) {
            console.error('Error fetching FAQs:', error);
        }
    }

    // Add or Update FAQ
    faqForm.addEventListener('submit', async function (event) {
        event.preventDefault(); // Prevent form submission from reloading the page

        const question = document.getElementById('faqQuestion').value;
        const answer = document.getElementById('faqAnswer').value;

        // Determine action based on editMode
        const action = editMode ? 'update' : 'add';
        const params = new URLSearchParams({
            action: action,
            question: question,
            answer: answer
        });

        if (editMode) {
            params.append('id', editId);
        }

        try {
            const response = await fetch('../function/fetchFAQ.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params
            });
            const data = await response.json();
            if (data.success) {
                faqForm.reset();
                fetchFAQs(); // Refresh FAQ list
                if (editMode) {
                    editMode = false;
                    editId = null;
                    faqForm.querySelector('button').textContent = 'Add FAQ';
                }
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Edit FAQ
    window.editFAQ = function (id, question, answer) {
        editMode = true;
        editId = id;
        document.getElementById('faqQuestion').value = question;
        document.getElementById('faqAnswer').value = answer;
        faqForm.querySelector('button').textContent = 'Update FAQ';
    };

    // Delete FAQ
    window.deleteFAQ = async function (id) {
        try {
            const response = await fetch('../function/fetchFAQ.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ action: 'delete', id: id })
            });
            const data = await response.json();
            if (data.success) {
                fetchFAQs();
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error deleting FAQ:', error);
        }
    };

    // Initial fetch of FAQs
    fetchFAQs();
});
