import { Application, Controller } from '@hotwired/stimulus';
import { getByTestId } from '@testing-library/dom';
import DomFirstFormController from '../../assets/controllers/dom_first_form_controller';

// Initialize Stimulus application
const application = Application.start();
application.register('dom-first-form', DomFirstFormController);

describe('DomFirstFormController', () => {
  let container;

  beforeEach(() => {
    container = document.createElement('div');
    container.innerHTML = `
      <div data-controller="dom-first-form">
        <select data-dom-first-form-target="salarieType">
          <option value="permanent">Permanent</option>
          <option value="temporaire">Temporaire</option>
        </select>
        <div data-dom-first-form-target="matriculeGroup">Matricule Group</div>
        <div data-dom-first-form-target="nomGroup">Nom Group</div>
        <div data-dom-first-form-target="prenomGroup">Prenom Group</div>
        <div data-dom-first-form-target="cinGroup">CIN Group</div>
      </div>
    `;
    document.body.appendChild(container);
  });

  afterEach(() => {
    document.body.removeChild(container);
  });

  test('should hide nomGroup, prenomGroup, and cinGroup when salarieType is permanent', () => {
    const salarieType = getByTestId(container, 'salarieType');
    const matriculeGroup = getByTestId(container, 'matriculeGroup');
    const nomGroup = getByTestId(container, 'nomGroup');
    const prenomGroup = getByTestId(container, 'prenomGroup');
    const cinGroup = getByTestId(container, 'cinGroup');

    salarieType.value = 'permanent';
    salarieType.dispatchEvent(new Event('change'));

    expect(matriculeGroup).not.toHaveClass('hidden');
    expect(nomGroup).toHaveClass('hidden');
    expect(prenomGroup).toHaveClass('hidden');
    expect(cinGroup).toHaveClass('hidden');
  });

  test('should hide matriculeGroup when salarieType is temporaire', () => {
    const salarieType = getByTestId(container, 'salarieType');
    const matriculeGroup = getByTestId(container, 'matriculeGroup');
    const nomGroup = getByTestId(container, 'nomGroup');
    const prenomGroup = getByTestId(container, 'prenomGroup');
    const cinGroup = getByTestId(container, 'cinGroup');

    salarieType.value = 'temporaire';
    salarieType.dispatchEvent(new Event('change'));

    expect(matriculeGroup).toHaveClass('hidden');
    expect(nomGroup).not.toHaveClass('hidden');
    expect(prenomGroup).not.toHaveClass('hidden');
    expect(cinGroup).not.toHaveClass('hidden');
  });
});