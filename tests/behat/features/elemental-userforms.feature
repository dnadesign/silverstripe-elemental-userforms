Feature: Use elemental userforms
  As a website user
  I want to use elemental userforms

  Background:
    Given I add an extension "DNADesign\Elemental\Extensions\ElementalPageExtension" to the "Page" class
    And the "group" "EDITOR" has permissions "Access to 'Pages' section"
    And a "page" "My page"

  Scenario: Operate elemental userforms
    Given I am logged in as a member of "EDITOR" group
    When I go to "/admin/pages"
    And I follow "My page"

    # Add userforms block
    And I press the "Add block" button
    And I wait for 1 seconds
    And I click on the ".font-icon-block-form" element
    And I wait for 5 seconds
    And I press the "Save" button

    # Open (non-inline) edit form
    And I click on the ".element-editor-header__expand" element
    And I fill in "Title" with "My form title"
    And I click the "Form Fields" CMS tab
    And I press the "Add Field" button
    And I fill in "Form_Fields_GridFieldEditableColumns_2_Title" with "My textfield 1"
    And I press the "Publish" button  

    # Assert that it saved
    Then the rendered HTML should contain "My textfield 1"
