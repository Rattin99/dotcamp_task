import { useState, useEffect } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import ServerSideRender from '@wordpress/server-side-render';

function Edit({ attributes, setAttributes }) {
    const [signups, setSignups] = useState([]);
    
    useEffect(() => {
        apiFetch({ path: '/wp/v2/task-signups' }).then(data => {
            setSignups(data);
        });
    }, []);

    return (
        <div>
            <SelectControl
                label={__('Select a person', 'task-plugin')}
                value={attributes.selectedPerson}
                options={[
                    { label: __('Select a person', 'task-plugin'), value: 0 },
                    ...signups.map(signup => ({
                        label: signup.name,
                        value: signup.id
                    }))
                ]}
                onChange={(selectedPerson) => setAttributes({ selectedPerson })}
            />
            <ServerSideRender
                block="task-plugin/signup-list"
                attributes={attributes}
            />
        </div>
    );
}

export default Edit;