

class Taglist {
    constructor(container,options){
        this.container = container;
        this.input = container.querySelector('input[type="text"]');
        this.suggestionsContainer = container.querySelector('.suggestions');
        this.tagsContainer = container.querySelector('.tags');
        this.hiddenInput = document.getElementById('hobbies');
        this.options = options;
        this.tags = [];
        
        console.log(options);
        this.init();
    }

    init() {
        this.input.addEventListener('input', () => this.showSuggestions());
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        document.addEventListener('click', (e) => this.handleClickOutside(e));
    }

    showSuggestions() {
        console.log('suggestions called')
        const value = this.input.value.toLowerCase();
        const filteredOptions = this.options.filter(option => 
            option.toLowerCase().includes(value) && !this.tags.includes(option)
        );

        this.suggestionsContainer.innerHTML = '';
        filteredOptions.forEach(option => {
            const div = document.createElement('div');
            div.textContent = option;
            div.addEventListener('click', () => this.addTag(option));
            this.suggestionsContainer.appendChild(div);
        });

        this.suggestionsContainer.style.display = filteredOptions.length ? 'block' : 'none';
    }

    addTag(tag) {
        if (this.tags.length >= 3) return;
        
        if (!this.tags.includes(tag)) {
            this.tags.push(tag);
            this.renderTags();
            this.input.value = '';
            this.suggestionsContainer.style.display = 'none';
            this.updateHiddenInput();
        }
    }

    removeTag(tag) {
        this.tags = this.tags.filter(t => t !== tag);
        this.renderTags();
        this.updateHiddenInput();
    }

    renderTags() {
        this.tagsContainer.innerHTML = '';
        this.tags.forEach(tag => {
            const tagElement = document.createElement('span');
            tagElement.textContent = tag;
            const removeButton = document.createElement('button');
            removeButton.textContent = '×';
            removeButton.addEventListener('click', () => this.removeTag(tag));
            tagElement.appendChild(removeButton);
            this.tagsContainer.appendChild(tagElement);
        });
    }

    updateHiddenInput() {
        this.hiddenInput.value = this.tags.join(',');
    }

    handleKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.input.value) {
                this.addTag(this.input.value);
            }
        } else if (e.key === 'Backspace' && !this.input.value && this.tags.length) {
            this.removeTag(this.tags[this.tags.length - 1]);
        } else if (e.key === 'ArrowDown' && this.suggestionsContainer.children.length) {
            e.preventDefault();
            this.suggestionsContainer.firstChild.focus();
        }
    }

    handleClickOutside(e) {
        if (!this.container.contains(e.target)) {
            this.suggestionsContainer.style.display = 'none';
        }
    }

}

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('hobbies-taglist');
    const options = ['fishing', 'running', 'coding', 'photography', 'singing', 'gardening', 'travelling'];
    new Taglist(container, options);

    console.log('taglist running...')
});