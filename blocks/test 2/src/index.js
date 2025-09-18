import { registerBlockType } from '@wordpress/blocks';
import metadata from '../block.json';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

registerBlockType( metadata.name, {
  edit: ( props ) => {
    const { attributes, setAttributes } = props;
    const { date, speaker, registrationUrl, recordingUrl, description } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __('Meetup Einstellungen', 'se-meetup') } initialOpen={ true }>
            <TextControl
              label={ __('Datum / Uhrzeit', 'se-meetup') }
              value={ date }
              onChange={ ( val ) => setAttributes({ date: val }) }
              placeholder="2025-09-01 19:00"
            />
            <TextControl
              label={ __('Speaker', 'se-meetup') }
              value={ speaker }
              onChange={ ( val ) => setAttributes({ speaker: val }) }
            />
            <TextControl
              label={ __('Anmeldung (URL)', 'se-meetup') }
              value={ registrationUrl }
              onChange={ ( val ) => setAttributes({ registrationUrl: val }) }
            />
            <TextControl
              label={ __('Aufzeichnung (URL)', 'se-meetup') }
              value={ recordingUrl }
              onChange={ ( val ) => setAttributes({ recordingUrl: val }) }
            />
          </PanelBody>
        </InspectorControls>

        <div { ...useBlockProps() } className="se-meetup-editor">
          <h3 className="meetup-speaker">{ speaker || __('Speaker Name', 'se-meetup') }</h3>
          <div className="meetup-date">{ date || __('Datum / Uhrzeit', 'se-meetup') }</div>
          <RichText
            tagName="div"
            className="meetup-description"
            value={ description }
            onChange={ ( val ) => setAttributes({ description: val }) }
            placeholder={ __('Kurzbeschreibung des Meetups...', 'se-meetup') }
          />
          <div className="meetup-links">
            { registrationUrl && <a href={ registrationUrl } target="_blank" rel="noreferrer noopener">{ __('Anmelden', 'se-meetup') }</a> }
            { recordingUrl && <a href={ recordingUrl } target="_blank" rel="noreferrer noopener">{ __('Aufzeichnung', 'se-meetup') }</a> }
          </div>
        </div>
      </Fragment>
    );
  },

  save: () => {
    // Dynamischer Block: Frontend wird serverseitig gerendert.
    return null;
  }
} );