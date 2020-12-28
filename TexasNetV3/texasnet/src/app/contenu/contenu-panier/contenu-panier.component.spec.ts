import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuPanierComponent } from './contenu-panier.component';

describe('ContenuPanierComponent', () => {
  let component: ContenuPanierComponent;
  let fixture: ComponentFixture<ContenuPanierComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuPanierComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuPanierComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
