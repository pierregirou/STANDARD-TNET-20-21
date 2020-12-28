import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuPromoComponent } from './contenu-promo.component';

describe('ContenuPromoComponent', () => {
  let component: ContenuPromoComponent;
  let fixture: ComponentFixture<ContenuPromoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuPromoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuPromoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
