const { registerBlockType } = wp.blocks;
const { TextControl } = wp.components;
const { InspectorControls } = wp.blockEditor;

registerBlockType('custom/referral-block', {
    title: 'Referral Code',
    icon: 'ticket',
    category: 'common',
    attributes: {
        referralCode: {
            type: 'string',
            default: ''
        }
    },
    edit: ({ attributes, setAttributes }) => {
        return (
            <div>
                <InspectorControls>
                    <TextControl
                        label="Referral Code"
                        value={attributes.referralCode}
                        onChange={(value) => setAttributes({ referralCode: value })}
                    />
                </InspectorControls>
                <div>
                    <TextControl
                        placeholder="Enter referral code"
                        value={attributes.referralCode}
                        onChange={(value) => setAttributes({ referralCode: value })}
                    />
                </div>
            </div>
        );
    },
    save: ({ attributes }) => {
        return (
            <div>
                <p>{attributes.referralCode}</p>
                <input type="hidden" name="referral_coupon" value={attributes.referralCode} />
            </div>
        );
    }
});
