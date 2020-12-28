import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuProduitsSelectionComponent } from './contenu-produits-selection.component';

describe('ContenuProduitsSelectionComponent', () => {
  let component: ContenuProduitsSelectionComponent;
  let fixture: ComponentFixture<ContenuProduitsSelectionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuProduitsSelectionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuProduitsSelectionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
