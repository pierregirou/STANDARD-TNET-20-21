import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuPromoSelectionComponent } from './contenu-promo-selection.component';

describe('ContenuPromoSelectionComponent', () => {
  let component: ContenuPromoSelectionComponent;
  let fixture: ComponentFixture<ContenuPromoSelectionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuPromoSelectionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuPromoSelectionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
