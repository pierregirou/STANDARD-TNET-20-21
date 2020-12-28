import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuRepresentantComponent } from './contenu-representant.component';

describe('ContenuRepresentantComponent', () => {
  let component: ContenuRepresentantComponent;
  let fixture: ComponentFixture<ContenuRepresentantComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuRepresentantComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuRepresentantComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
