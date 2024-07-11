import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';

registerBlockType('task-plugin/signup-list', {
    title: __('Signup List', 'task-plugin'),
    icon: 'list-view',
    category: 'widgets',
    edit: function(props) {
        console.log('Rendering edit function');
        return createElement(
            'div',
            null,
            createElement('p', null, 'Hello from the editor!'),
            createElement(ServerSideRender, {
                block: "task-plugin/signup-list",
                attributes: props.attributes
            })
        );

    },
    save: function() {
        return  null; // Rendered on the server
    },
});