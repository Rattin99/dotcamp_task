import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import Save from './save';

registerBlockType('task-plugin/signup-list', {
    title: __('Signup List', 'task-plugin'),
    icon: 'list-view',
    category: 'widgets',
    attributes: {
        selectedPerson: {
            type: 'number',
            default: 0
        }
    },
    edit: Edit,
    save: Save
});